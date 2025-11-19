<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb;

interface EsbListenerInterface
{
    /**
     * Устанавливает обработчик события при получении сообщения.
     *
     * @param callable $callback Функция, вызываемая при получении нового сообщения.
     */
    public function onReceived(callable $callback): void;

    /**
     * Запускает слушателя.
     */
    public function run(string $queue): void;
}