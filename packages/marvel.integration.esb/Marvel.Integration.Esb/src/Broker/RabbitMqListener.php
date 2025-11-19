<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb\Broker;

use Marvel\Integration\Esb\EsbListenerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPIOException;
use RuntimeException;
use Exception;
use InvalidArgumentException;

class RabbitMqListener implements EsbListenerInterface
{
    private BrokerConfigInterface $config;
    private ?AMQPStreamConnection $connection = null;
    private ?\PhpAmqpLib\Channel\AMQPChannel $channel = null;
    private $onReceived;
    private int $maxRuntime = 0;
    private bool $shouldStop = false;
    private int $reconnectDelay = 5;
    private int $startTime = 0;
    private string $currentQueue = '';

    public function __construct(BrokerConfigInterface $config)
    {
        if ($config === null) {
            throw new InvalidArgumentException('config не может быть null');
        }
        $this->config = $config;
    }

    private function log(string $message): void
    {
        echo date('[Y-m-d H:i:s] ') . $message . PHP_EOL;
    }

    private function connect(): void
    {
        while (true) {
            try {
                $this->connection = new AMQPStreamConnection(
                    $this->config->getHostName(),
                    $this->config->getPort(),
                    $this->config->getUserName(),
                    $this->config->getPassword()
                );

                $this->channel = $this->connection->channel();

                register_shutdown_function(function ($ch, $conn) {
                    if ($ch && $ch->is_open()) $ch->close();
                    if ($conn && $conn->isConnected()) $conn->close();
                }, $this->channel, $this->connection);

                $this->log("✅ Подключено к RabbitMQ");

                // После подключения сразу выводим запуск слушателя
                if ($this->currentQueue !== '') {
                    $this->log("▶️ Запуск слушателя для очереди: {$this->currentQueue}");
                }

                break;
            } catch (Exception $e) {
                $this->log("⚠️ Не удалось подключиться к RabbitMQ: {$e->getMessage()}");
                $this->log("⏳ Повторная попытка через {$this->reconnectDelay} сек...");
                sleep($this->reconnectDelay);
            }
        }
    }

    public function onReceived(callable $callback): void
    {
        $this->onReceived = $callback;
    }

    public function run(string $queue, int $maxRuntime = 0): void
    {
        if (!in_array($queue, $this->config->getQueues(), true)) {
            throw new InvalidArgumentException('queue должен быть в списке разрешённых');
        }

        $this->currentQueue = $queue;
        $this->maxRuntime = $maxRuntime;
        $this->shouldStop = false;

        if (function_exists('pcntl_signal')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, fn() => $this->handleStopSignal("SIGTERM"));
            pcntl_signal(SIGINT, fn() => $this->handleStopSignal("SIGINT"));
        }

        while (!$this->shouldStop) {
            $this->startTime = time(); // сброс таймера

            try {
                if (!$this->connection || !$this->connection->isConnected() || !$this->channel || !$this->channel->is_open()) {
                    $this->reconnect(); // переподключение
                }

                $this->channel->queue_declare($queue, false, true, false, false);

                $callback = fn(AMQPMessage $msg) => $this->handleReceivedMessage($msg);
                $this->channel->basic_consume($queue, '', false, false, false, false, $callback);

                while ($this->channel->is_consuming() && !$this->shouldStop) {
                    // Проверка времени
                    if ($this->maxRuntime > 0 && (time() - $this->startTime > $this->maxRuntime)) {
                        $this->log("⏰ Превышено время работы: {$this->maxRuntime} сек — переподключаемся...");
                        $this->reconnect();
                        break;
                    }

                    try {
                        $this->channel->wait(null, false, 5);
                    } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
                        continue;
                    } catch (AMQPConnectionClosedException | AMQPIOException $e) {
                        $this->log("⚠️ Потеряно соединение: {$e->getMessage()} — переподключение...");
                        $this->reconnect();
                        break;
                    }
                }
            } catch (Exception $e) {
                $this->log("❌ Ошибка: {$e->getMessage()} — переподключение...");
                $this->reconnect();
            }
        }

        $this->close();
        $this->log("✅ Слушатель {$queue} остановлен корректно.");
    }

    private function handleStopSignal(string $signal): void
    {
        $this->log("🛑 Получен сигнал {$signal}, завершаем работу...");
        $this->shouldStop = true;
    }

    protected function handleReceivedMessage(AMQPMessage $message): void
    {
        $body = $message->getBody();
        $deserialized = json_decode($body);

        if ($deserialized === null) {
            $this->log("⚠️ Некорректный формат сообщения: {$body}");
            $message->ack();
            return;
        }

        $properties = [
            'routingKey' => $message->get('routing_key'),
            'consumerTag' => $message->get('consumer_tag'),
            'deliveryTag' => $message->get('delivery_tag'),
            'exchange' => $message->get('exchange'),
        ];

        try {
            if ($this->onReceived !== null) {
                ($this->onReceived)($this, $deserialized, $properties);
            }
            $message->ack();
        } catch (Exception $e) {
            $this->log("❌ Ошибка обработки сообщения: {$e->getMessage()}");
            $message->nack(true);
        }
    }

    private function reconnect(): void
    {
        $this->close();
        $this->log("🔄 Переподключение через {$this->reconnectDelay} сек...");
        sleep($this->reconnectDelay);
        $this->connect();
        $this->startTime = time(); // сброс таймера
    }

    public function close(): void
    {
        if ($this->channel && $this->channel->is_open()) $this->channel->close();
        if ($this->connection && $this->connection->isConnected()) $this->connection->close();

        $this->channel = null;
        $this->connection = null;
        $this->log("🔒 Соединение закрыто.");
    }
}
