<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb;

/**
 * Интерфейс сообщения для отправки в шину.
 */
interface EsbMessageInterface
{
    /**
     * Получение тела сообщения в виде строки (аналог byte[] в C#).
     */
    public function getBody(): array;

    /**
     * Получение идентификатора корреляции.
     */
    public function getCorrelationId(): string;

    /**
     * Получение даты и времени создания сообщения.
     */
    public function getCreatedTime(): \DateTimeImmutable;

    /**
     * Получение имени или идентификатора системы-отправителя.
     */
    public function getSourceAppName(): string;

    /**
     * Получение имени или идентификатора системы-получателя.
     */
    public function getTargetAppName(): string;

    /**
     * Получение формата сообщения (JSON, XML и т. п.).
     */
    public function getMessageFormat(): EsbMessageFormat;

    /**
     * Сериализация объекта в строку (аналог byte[] в C#).
     */
    public function serialize(): string;
}