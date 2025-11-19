<?php

declare(strict_types=1);

namespace Marvel\Infrastructure\FileStorage;

use InvalidArgumentException;
use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;
use Aws\Exception\AwsException;

class MinioStorageFactory implements MinioStorageFactoryInterface
{
    private $config;

    public function __construct(MinioStorageConfig $config)
    {
        if ($config === null) {
            throw new InvalidArgumentException('config cannot be null');
        }

        $this->config = $config;
    }

    public function createClient(): S3ClientInterface
    {
        $scheme = $this->config->ssl ? 'https' : 'http';
        $endpoint = sprintf('%s://%s:%d', $scheme, $this->config->address, $this->config->port);

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->config->region,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true, // обязательно для MinIO
            'credentials' => [
                'key' => $this->config->login,
                'secret' => $this->config->password,
            ],
        ]);

        return $s3Client;
    }
}