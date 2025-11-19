<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb;

interface EsbSenderInterface
{
    /**
     * Отправка сообщения
     *
     * @param string $sourceAppName Имя или идентификатор системы-отправителя
     * @param string $targetAppName Имя или идентификатор системы-получателя
     * @param array $message Тело сообщения (в PHP строки могут содержать бинарные данные)
     * @param EsbMessageFormat $messageFormat Формат сообщения (например, EsbMessageFormat::JSON)
     * @param string|null $correlationId Идентификатор для отслеживания сквозной передачи сообщения между системами
     */
    public function send(
        string $sourceAppName,
        string $targetAppName,
        array $message,
        EsbMessageFormat $messageFormat,
        ?string $correlationId = null
    ): void;
}