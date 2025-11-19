<?php

declare(strict_types=1);

namespace Marvel\Infrastructure\FileStorage;

use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;

interface MinioStorageFactoryInterface
{
    /**
     * Создать клиента Minio
     */
    public function createClient(): S3ClientInterface;
}