<?php

require_once '/vendor/autoload.php';

use Marvel\Infrastructure\FileStorage;
use Marvel\Infrastructure\FileStorage\MinioStorage;
use Marvel\Infrastructure\FileStorage\MinioStorageConfig;
use Marvel\Infrastructure\FileStorage\MinioStorageFactory;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\Utils;

echo "Старт \n" ;

// composer require aws/aws-sdk-php
// composer require guzzlehttp/psr7

$sourceFileName = __DIR__ . DIRECTORY_SEPARATOR . "picture/Programming-new.jpg";
$targetFileName = __DIR__ . DIRECTORY_SEPARATOR . "picture/down/Programming-down.jpg";

// Конфигурация minio
$config = new MinioStorageConfig(
    "192.168.229.197",
    9000,
    "Gz89A6Yi6Hvl4KqJ5pdn",
    "XWeTjKqnCw2LILMjPius3FNRAWZXmVR05Kc1Zsyk",
    "ru-1",
    false
);
$bucket = "b2b-test";

// Создаём фабрику клиента
$minioClientFactory = new MinioStorageFactory($config);
$minioClient = new MinioStorage($minioClientFactory->createClient(), $bucket);

// Загрузка файла (в PHP читаем файл как resource)
$readStream = fopen($sourceFileName, 'rb');
if (!$readStream) {
    throw new RuntimeException("Не удалось открыть файл для чтения: $sourceFileName");
}

$psrStream = Utils::streamFor($readStream);

$uploadedFileUrl = $minioClient->uploadFile($psrStream, "Programming-new.jpg");
fclose($readStream);
echo "Ссылка на загруженный файл - $uploadedFileUrl\n";

// Если файл небольшой, можно прочитать все содержимое целиком через getContents() и сохранить
//$minioClient->downloadFile($uploadedFileUrl, function(StreamInterface $stream) {
//    $targetFileName = __DIR__ . DIRECTORY_SEPARATOR . "down/Programming-2.jpg";
//    $content = $stream->getContents();
//    file_put_contents($targetFileName, $content);
//    return  true;
//});

$minioClient->downloadFile($uploadedFileUrl, function (StreamInterface $stream) {
    $localFilePath = __DIR__ . DIRECTORY_SEPARATOR . "picture/down/Programming-down.jpg";

    // Открываем локальный файл для записи (wb — бинарная запись)
    $fileHandle = fopen($localFilePath, 'wb');
    if ($fileHandle === false) {
        throw new RuntimeException("Не удалось открыть файл для записи: $localFilePath");
    }

    // Читаем поток по частям и записываем, чтобы не держать весь файл в памяти
    while (!$stream->eof()) {
        fwrite($fileHandle, $stream->read(1024 * 1024)); // читаем по 1МБ
    }

    fclose($fileHandle);

    return true; // Можно вернуть что угодно, например true при успехе
});

echo "Скачан файл по ссылке $uploadedFileUrl. Сохранён в $targetFileName\n";
