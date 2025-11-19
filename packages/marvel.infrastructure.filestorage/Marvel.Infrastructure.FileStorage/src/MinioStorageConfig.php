<?php

declare(strict_types=1);

namespace Marvel\Infrastructure\FileStorage;

use InvalidArgumentException;

class MinioStorageConfig
{
    public string $address;
    public int $port;
    public string $login;
    public string $password;
    public bool $ssl;
    public ?string $region;

    /**
     * Конструктор
     *
     * @param string $address Адрес сервера MiniO API. ip-адрес или имя хоста.
     * @param int $port Порт MiniO API
     * @param string $login Логин
     * @param string $password Пароль
     * @param string $region Регион расположения сервера, обрабатывающего запрос (по умолчанию пусто)
     * @param bool $ssl Признак необходимости использования SSL (по умолчанию false)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $address, int $port, string $login, string $password, string $region = "", bool $ssl = false)
    {
        if (empty($address)) {
            throw new InvalidArgumentException("Address cannot be empty");
        }

        if (empty($login)) {
            throw new InvalidArgumentException("Login cannot be empty");
        }

        if (empty($password)) {
            throw new InvalidArgumentException("Password cannot be empty");
        }

        if ($port <= 0) {
            throw new InvalidArgumentException("Port must be greater than zero");
        }

        $this->address = $address;
        $this->port = $port;
        $this->login = $login;
        $this->password = $password;
        $this->ssl = $ssl;
        $this->region = $region;
    }
}