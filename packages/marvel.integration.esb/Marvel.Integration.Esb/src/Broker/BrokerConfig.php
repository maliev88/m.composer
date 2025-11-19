<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb\Broker;

use InvalidArgumentException;

class BrokerConfig implements BrokerConfigInterface
{
    private string $hostName;
    private int $port;
    private string $userName;
    private string $password;
    private array $queues;

    /**
     * Конструктор
     *
     * @param string $hostName ip-адрес или имя сервера
     * @param int $port Порт сервера
     * @param string $userName Имя пользователя для подключения к серверу
     * @param string $password Пароль для подключения к серверу
     * @param array $queues Список названий очередей для сообщений
     * @throws InvalidArgumentException
     */
    public function __construct(string $hostName, int $port, string $userName, string $password, array $queues)
    {
        if (empty($hostName)) {
            throw new InvalidArgumentException('Host name cannot be null or empty.');
        }

        if (empty($userName)) {
            throw new InvalidArgumentException('User name cannot be null or empty.');
        }

        if (empty($password)) {
            throw new InvalidArgumentException('Password cannot be null or empty.');
        }

        if (empty($queues)) {
            throw new InvalidArgumentException('Queues cannot be empty array');
        }

        if ($port <= 0) {
            throw new InvalidArgumentException('Port must be greater than zero.');
        }

        $this->hostName = $hostName;
        $this->port = $port;
        $this->userName = $userName;
        $this->password = $password;
        $this->queues = $queues;
    }

    public function getHostName(): string
    {
        return $this->hostName;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getQueues(): array
    {
        return $this->queues;
    }
}