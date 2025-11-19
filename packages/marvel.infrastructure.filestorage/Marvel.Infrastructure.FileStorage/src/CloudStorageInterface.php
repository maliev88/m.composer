<?php

declare(strict_types=1);

namespace Marvel\Infrastructure\FileStorage;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Promise\PromiseInterface;

interface CloudStorageInterface
{
    /**
     * Загрузить файл из потока
     */
    public function uploadFile(StreamInterface $fileStream, string $cloudFileName): string;

    /**
     * Асинхронно загрузить файл из потока
     */
    public function uploadFileAsync(StreamInterface $fileStream, string $cloudFileName): PromiseInterface;

    /**
     * Скачать файл по URL
     */
    public function downloadFile(string $url, callable $callback): void;

    /**
     * Асинхронно скачать файл по URL
     */
    public function downloadFileAsync(string $url, callable $callback): PromiseInterface;
}