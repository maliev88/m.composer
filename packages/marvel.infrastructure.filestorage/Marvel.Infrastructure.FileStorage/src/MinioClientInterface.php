<?php

declare(strict_types=1);

namespace Marvel\Infrastructure\FileStorage;

use Psr\Http\Message\StreamInterface;

interface MinioClientInterface
{
    /**
     * Загрузить объект
     */
    public function uploadObject(string $bucket, string $objectName, $stream, int $size, string $contentType): string;

    /**
     * Скачать объект
     */
    public function downloadObject(string $bucket, string $objectName, callable $streamHandler): void;

}