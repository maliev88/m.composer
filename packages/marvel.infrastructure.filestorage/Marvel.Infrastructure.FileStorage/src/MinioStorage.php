<?php

declare(strict_types=1);

namespace Marvel\Infrastructure\FileStorage;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Psr\Http\Message\StreamInterface;
use Exception;
use RuntimeException;
use InvalidArgumentException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Create;

class MinioStorage implements CloudStorageInterface
{
    private string $bucket;
    private S3Client $minioClient;

    private const MINIO_CONTENT_TYPE = 'application/octet-stream';

    // Конструктор с фабрикой (аналогично можно реализовать, если у вас есть фабрика)
    public function __construct($minioClientOrFactory, string $bucket)
    {
        if (empty($bucket)) {
            throw new InvalidArgumentException('Bucket name cannot be empty');
        }

        if (is_object($minioClientOrFactory) && method_exists($minioClientOrFactory, 'CreateClient')) {
            $this->minioClient = $minioClientOrFactory->CreateClient();
        } elseif ($minioClientOrFactory instanceof S3Client) {
            $this->minioClient = $minioClientOrFactory;
        } else {
            throw new InvalidArgumentException('Неправильный параметр для клиента Minio');
        }

        $this->bucket = $bucket;
    }

    // Метод скачивания файла с callback
    public function downloadFile(string $url, callable $callback): void
    {
        if (empty($url)) {
            throw new InvalidArgumentException('URL не должен быть пустым');
        }

        $parsedUrl = parse_url($url);

        if (empty($parsedUrl['path']) || strlen($parsedUrl['path']) <= 1) {
            throw new RuntimeException("Отсутствует имя файла в URL: $url");
        }

        $bucketPattern = '/' . $this->bucket . '/';
        $pos = strpos($parsedUrl['path'], $bucketPattern);

        if ($pos === false) {
            throw new RuntimeException("URL не содержит имя бакета в пути: $url");
        }

        $cloudFileName = substr($parsedUrl['path'], $pos + strlen($bucketPattern));

        try {
            // Проверяем что файл существует
            $this->minioClient->headObject([
                'Bucket' => $this->bucket,
                'Key' => $cloudFileName,
            ]);

            // Получаем файл синхронно
            $result = $this->minioClient->getObject([
                'Bucket' => $this->bucket,
                'Key' => $cloudFileName,
            ]);
        } catch (AwsException $e) {
            throw new RuntimeException("Ошибка при получении файла: " . $e->getAwsErrorMessage());
        }

        $body = $result['Body']; // Объект PSR-7 StreamInterface

        // Передаем поток в callback и возвращаем результат
        $callback($body);

    }

    // Аналог асинхронного метода скачивания файла — здесь тоже реализуем синхронно
    public function downloadFileAsync(string $url, callable $callback): PromiseInterface
    {
        if (empty($url)) {
            throw new InvalidArgumentException('URL не должен быть пустым');
        }

        $parsedUrl = parse_url($url);

        if (empty($parsedUrl['path']) || strlen($parsedUrl['path']) <= 1) {
            return Create::rejectionFor(new RuntimeException("Отсутствует имя файла в URL: $url"));
        }

        $bucketPattern = '/' . $this->bucket . '/';
        $pos = strpos($parsedUrl['path'], $bucketPattern);

        if ($pos === false) {
            return Create::rejectionFor(new RuntimeException("URL не содержит имя бакета в пути: $url"));
        }

        $cloudFileName = substr($parsedUrl['path'], $pos + strlen($bucketPattern));

        // Используем getObjectAsync, чтобы получить объект асинхронно
        return $this->minioClient->getObjectAsync([
            'Bucket' => $this->bucket,
            'Key' => $cloudFileName,
        ])->then(function ($result) use ($callback) {
            $body = $result['Body']; // Psr\Http\Message\StreamInterface
            return $callback($body);
        }, function ($error) {
            // Прокидываем ошибку дальше
            throw new RuntimeException('Ошибка при загрузке объекта: ' . $error->getMessage());
        });
    }

    // Загрузка файла, возвращает URL файла
    public function uploadFile(StreamInterface $fileStream, string $cloudFileName): string
    {
        if (empty($cloudFileName)) {
            throw new InvalidArgumentException('Имя файла для загрузки не может быть пустым');
        }

        if (!$fileStream instanceof StreamInterface && !is_resource($fileStream) && !is_string($fileStream)) {
            throw new InvalidArgumentException('fileStream должен быть ресурсом, строкой или PSR-7 StreamInterface');
        }

        $this->minioClient->putObject([
            'Bucket' => $this->bucket,
            'Key' => $cloudFileName,
            'Body' => $fileStream,
            'ContentType' => self::MINIO_CONTENT_TYPE,
        ]);

        // Формируем URL файла, пример формата - зависит от вашей конфигурации Minio
        $endpointUri = $this->minioClient->getEndpoint();

        $url = (string)$endpointUri . '/' . $this->bucket . '/' . $cloudFileName;

        return $url;
    }

    public function uploadFileAsync(StreamInterface $fileStream, string $cloudFileName): PromiseInterface
    {
        // Предполагается, что у вас есть клиент AWS S3 (или Minio) в $this->client
        // Метод PutObject асинхронно загружает файл в указанный бакет
        return $this->minioClient->putObjectAsync([
            'Bucket' => $this->bucket,
            'Key' => $cloudFileName,           // Имя файла в облаке
            'Body' => $fileStream,             // Поток файла для загрузки
        ]);
    }
}