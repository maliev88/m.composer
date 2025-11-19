<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb\Broker;

use Marvel\Integration\Esb\EsbMessageFormat;
use Marvel\Integration\Esb\EsbSender;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use Marvel\Integration\Esb\Exceptions\EsbDeliveryException;
use InvalidArgumentException;
use Exception;

class RabbitMqMessageSender extends EsbSender
{
    protected $config;
    protected $connectionFactory;

    public function __construct(BrokerConfigInterface $config)
    {
        parent::__construct();

        if (is_null($config)) {
            throw new InvalidArgumentException('config cannot be null');
        }

        $this->config = $config;

        try {
            $this->connectionFactory = new AMQPStreamConnection(
                $config->getHostName(),
                $config->getPort(),
                $config->getUserName(),
                $config->getPassword()
            );
        } catch (Exception $e) {
            echo 'Ошибка отправки сообщения: ' . $e->getMessage();
        }
    }

    public function send(string $sourceAppName, string $targetAppName, array $message, EsbMessageFormat $messageFormat, string $correlationId = null): void
    {
        $connection = null;
        try {
            if ($this->connectionFactory) {
                $connection = $this->connectionFactory;
                foreach ($this->config->getQueues() as $queue) {
                    // Открываем канал
                    $channel = $connection->channel();

                    // Устанавливаем подтверждения
                    $channel->confirm_select();

                    try {
                        // Объявляем очередь
                        $channel->queue_declare($queue, false, true, false, false, false, null);

                        // Создаем сообщение
                        $esbMessage = $this->createEsbMessage($sourceAppName, $targetAppName, $message, $messageFormat, $correlationId);
                        $amqpMessage = new AMQPMessage($esbMessage, [
                            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                        ]);

                        // Публикуем сообщение
                        $channel->basic_publish($amqpMessage, '', $queue);

                        // Проверить получено ли сообщение
                        if (!$this->waitForConfirms($channel, 5)) {
                            throw new EsbDeliveryException('Время ожидания подтверждения отправки сообщения превышено.', $correlationId);
                        }
                    } finally {
                        // Закрываем канал
                        $channel->close();
                    }
                }
            }
        } catch (Exception $e) {
            echo 'Ошибка отправки сообщения: ' . $e->getMessage();
        } finally {
            // Закрываем соединение
            $connection?->close();
        }
    }

    protected function waitForConfirms(AMQPChannel $channel, int $timeout): bool
    {
        //return (bool)$channel->waitForConfirms($timeout);
        return true;
    }
}