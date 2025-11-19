<?php
require_once __DIR__ . '/Packages/Marvel.Integration.Esb/Marvel.Integration.Esb/vendor/autoload.php';
// require_once __DIR__ . '/packages/marvel.infrastructure.filestorage/Marvel.Infrastructure.FileStorage/vendor/autoload.php';

use Marvel\Integration\Esb;
use Marvel\Integration\Esb\EsbMessageFormat;
use Marvel\Integration\Esb\Broker\BrokerConfig;
use Marvel\Integration\Esb\Broker\RabbitMqListener;
use Marvel\Integration\Esb\Broker\RabbitMqMessageSender;


//use Marvel\Infrastructure\FileStorage;
//use Marvel\Infrastructure\FileStorage\MinioStorage;
//use Marvel\Infrastructure\FileStorage\MinioStorageConfig;
//use Marvel\Infrastructure\FileStorage\MinioStorageFactory;
//use Psr\Http\Message\StreamInterface;
//use GuzzleHttp\Psr7\Utils;

echo "Старт \n" ;


// Получение - начало -
$brokerConfigIn = new BrokerConfig(
    "rabbitmq-test.dev.gkm",
    5672,
    "esb_user",
    "~g3)s6Vevz;n47",
    ["DatareonGostatOut"]
);
// Создание экземпляра слушателя
$rabbitMqListener = new RabbitMqListener($brokerConfigIn);

// Подписка на событие OnReceived
$rabbitMqListener->onReceived(function ($sender, $message, $properties) {
    echo date('Y-m-d H:i:s') . " - получено новое сообщение\n";
    echo "Сообщение:\n";
    // echo $message->body . "\n\n\n";

    // Преобразуем массив байтов обратно в строку
    $jsonString = implode(array_map("chr", $message->body));
    // Декодируем JSON обратно в массив
    $decodedArray = json_decode($jsonString, true);

    // Проверка результата body
    print_r($decodedArray);

});

// Запуск слушателя
$rabbitMqListener->run('DatareonGostatOut',60);
// Получение - конец -

echo "End \n";


/*
// отправка - начало -
$brokerConfigOut = new BrokerConfig(
    "rabbitmq-test.dev.gkm",
    5672,
    "esb_user",
    "~g3)s6Vevz;n47",
    ["DatareonGostatOut"]
);

$sendMessageThread = function () use ($brokerConfigOut) {
    $messageBody = [
        'body' => [
            "itemID" => "AE10 512+12 Rippling Blue",
            "itemName" => 'AE10 PHANTOM V Fold2 5G, 512+12 Rippling Blue, Disp1 7.85" 2K+ Fold, 20:9, 2296x2000 Mpix, Disp2 6.42" FHD+3D, 20:9, 1080x2550, 2.85GHz, 8 Core, 12GB RAM,512GB, up to 2TB flash, 50Мп/32Мп/16Мп, 2Sim, LTE, 4G, 5G, BT v5.3, WiFi, NFC, GPS/Glonass/Beidou, Type-C, 4860mAh, Android 13, 299g',
            "vendorCode" => "AE10 PHANTOM V Fold2 5G 512+12 Blue",
            "itemDescription" => 'AE10 PHANTOM V Fold2 5G, 512+12 Rippling Blue, Disp1 7.85" 2K+ Fold, 20:9, 2296x2000 Mpix, Disp2 6.42" FHD+3D, 20:9, 1080x2550, 2.85GHz, 8 Core, 12GB RAM,512GB, up to 2TB flash, 50Мп/32Мп/16Мп, 2Sim, LTE, 4G, 5G, BT v5.3, WiFi, NFC, GPS/Glonass/Beidou, Type-C, 4860mAh, Android 13, 299g',
            "itemPrintableName" => 'AE10 PHANTOM V Fold2 5G, 512+12 Rippling Blue, Disp1 7.85" 2K+ Fold, 20:9, 2296x2000 Mpix, Disp2 6.42" FHD+3D, 20:9, 1080x2550, 2.85GHz, 8 Core, 12GB RAM,512GB, up to 2TB flash, 50Мп/32Мп/16Мп, 2Sim, LTE, 4G, 5G, BT v5.3, WiFi, NFC, GPS/Glonass/Beidou, Type-C, 4860mAh, Android 13, 299g',
            "productMarkType" => "Физический товар",
            "itemCategory" => "PDA",
            "wareSubGroupCode" => "PDA",
            "categoryID" => "Мобил_Коммуник",
            "producerId" => "Tecno",
            "model" => "",
            "series" => "",
            "vat" => "20",
            "vatCode" => "Т20",
            "isNew" => "",
            "eol" => "",
            "eolDate" => "",
            "notificationNumber" => "KZ0000007891",
            "blocked" => "",
            "inventNapr" => "MOBILE",
            "budgetItemGroupID" => "MOB_Tecno",
            "inventNameBuhId" => "Смартфон",
            "inventNameBuhId2" => 'AE10 PHANTOM V Fold2 5G, 512+12 Rippling Blue, Disp1 7.85" 2K+ Fold, 20:9, 2296x2000 Mpix, Disp2 6.42" FHD+3D, 20:9, 1080x2550, 2.85GHz, 8 Core, 12GB RAM,512GB, up to 2TB flash, 50Мп/32Мп/16Мп, 2Sim, LTE, 4G, 5G, BT v5.3, WiFi, NFC, GPS/Glonass/Beidou, Type-C, 4860mAh, Android 13, 299g',
            "height" => "20.4",
            "width" => "18.7",
            "length" => "5",
            "weight" => "0.738",
            "measureUnit" => "796",
            "eans" => Array(
                "0" => "4894947045431",
            ),
            "tnvedCodes" => Array(
                "0" => "8517130000",
            ),
            "state" => "update",
            "messageID" => "31fd2251-0009-45c2-b3e4-dabd3a9d201b",
            "timestamp" => "2025-10-08T10:06:35",
        ],
    ];

    $sender = new RabbitMqMessageSender($brokerConfigOut);

    $sender->send(
        "DemoApp",
        "Datareon",
        $messageBody,
        EsbMessageFormat::Json,
        uniqid()
    );

    echo date('Y-m-d H:i:s') . " - сообщение отправлено\n";
};

$sendMessageThread();
// отправка - конец -
*/











/*
$sourceFileName = __DIR__ . DIRECTORY_SEPARATOR . "Programming-new.jpg";
$targetFileName = __DIR__ . DIRECTORY_SEPARATOR . "down\Programming-down.jpg";

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
    $localFilePath = __DIR__ . DIRECTORY_SEPARATOR . "down\Programming-down.jpg";

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

echo "Скачан файл по ссылке $uploadedFileUrl. Сохранён в $targetFileName\n";*/



// Асинхронного аналога в PHP нет, делаем синхронное скачивание
//$minioClient->downloadFile($uploadedFileUrl, function ($stream) use ($targetFileName) {
//    $writeStream = fopen($targetFileName, 'wb');
//    if (!$writeStream) {
//        throw new RuntimeException("Не удалось открыть файл для записи: $targetFileName");
//    }
//    stream_copy_to_stream($stream, $writeStream);
//    fclose($writeStream);
//});


/*
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://192.168.229.197:9000/b2b-test/test1.jpg');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

$post = array(
    'file' => '@' .realpath('/C:/Users/komarevtsev_am/Work/Projects/ngp/Packages/Marvel.Infrastructure/Marvel.Infrastructure.FileStorage.DemoApp/Programming.jpg')
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

$headers = array();
$headers[] = 'Content-Type: image/jpeg';
$headers[] = 'X-Amz-Content-Sha256: e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';
$headers[] = 'X-Amz-Date: 20250506T095358Z';
$headers[] = 'Authorization: AWS4-HMAC-SHA256 Credential=Gz89A6Yi6Hvl4KqJ5pdn/20250506/ru-1/s3/aws4_request, SignedHeaders=content-length;content-type;host;x-amz-content-sha256;x-amz-date, Signature=cb5c317041fd5f44a84a05376a7cd0c07f2eb2051f68d04cd8a4e37225518182';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);*/




/*
use Aws\S3\S3Client;
use Psr\Http\Message\StreamInterface;

namespace Marvel\Infrastructure\FileStorage;



use GuzzleHttp\Promise\PromiseInterface;

class MinioStorage implements ICloudStorage
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
        // Синхронное выполнение downloadFileAsync (PHP не поддерживает async/await)
        $this->downloadFileAsync($url, $callback);
    }

    // Аналог асинхронного метода скачивания файла — здесь тоже реализуем синхронно
    public function downloadFileAsync(string $url, callable $callback): void
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

        // Проверяем существование объекта
        try {
            $this->minioClient->headObject([
                'Bucket' => $this->bucket,
                'Key' => $cloudFileName,
            ]);
        } catch (Aws\Exception\AwsException $e) {
            throw new RuntimeException("Файл не найден: " . $e->getAwsErrorMessage());
        }

        // Получаем объект
        $result = $this->minioClient->getObject([
            'Bucket' => $this->bucket,
            'Key' => $cloudFileName,
        ]);

        $body = $result['Body']; // Psr\Http\Message\StreamInterface

        // Передаем поток в callback
        $callback($body);
    }

    // Загрузка файла, возвращает URL файла
    public function uploadFile($fileStream, string $cloudFileName): string
    {
        if (empty($cloudFileName)) {
            throw new InvalidArgumentException('Имя файла для загрузки не может быть пустым');
        }

        if (!$fileStream instanceof StreamInterface && !is_resource($fileStream) && !is_string($fileStream)) {
            throw new InvalidArgumentException('fileStream должен быть ресурсом, строкой или PSR-7 StreamInterface');
        }

        $this->minioClient->putObject([
            'Bucket' => $this->bucket,
            'Key' => $cloudFileName,           'Body' => $fileStream,
            'ContentType' => self::MINIO_CONTENT_TYPE,
        ]);

        // Формируем URL файла, пример формата - зависит от вашей конфигурации Minio
        $endpointUri = $this->minioClient->getEndpoint();

        $url = (string)$endpointUri . '/' . $this->bucket . '/' . $cloudFileName;

        return $url;
    }

    public function uploadFileAsync($fileStream, string $cloudFileName): PromiseInterface
    {
        // TODO: Implement uploadFileAsync() method.
    }
}




interface ICloudStorage
{

    public function uploadFile(StreamInterface $fileStream, string $cloudFileName): string;


    public function uploadFileAsync(
        StreamInterface $fileStream,
        string          $cloudFileName,
        ?callable       $cancellationCallback = null
    );


    public function downloadFile(string $uri, callable $callback): void;


    public function downloadFileAsync(
        string    $uri,
        callable  $callback,
        ?callable $cancellationCallback = null
    );
}
*/

/*
use Aws\S3\S3Client;
use Psr\Http\Message\StreamInterface;

class MinioStorage implements ICloudStorage
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

        if (is_callable([$minioClientOrFactory, 'CreateClient'])) {
            // Если передан фабричный объект, создаём клиент через фабрику
            $this->minioClient = $minioClientOrFactory->CreateClient();
        } elseif ($minioClientOrFactory instanceof S3Client) {
            $this->minioClient = $minioClientOrFactory;
        } else {
            throw new InvalidArgumentException('Неправильный параметр для клиента Minio');
        }

        $this->bucket = $bucket;
    }

    // Скачивание файла с колбеком передачи содержимого (поток)
    public function downloadFile(string $url, callable $callback): void
    {
        // На PHP обычно нет async, делаем синхронно
        $this->downloadFileAsync($url, $callback);
    }

    // Функция скачивания файла с вызовом callback (stream) - аналог async
    public function downloadFileAsync(string $url, callable $callback): void
    {
        if (empty($url)) {
            throw new InvalidArgumentException('URL не должен быть пустым');
        }

        $parsed = parse_url($url);

        if (empty($parsed['path']) || strlen($parsed['path']) <= 1) {
            throw new RuntimeException("Отсутствует имя файла в URL: $url");
        }

        // Из пути вынимаем имя файла относительно bucket
        $bucketPattern = '/' . $this->bucket . '/';
        $pos = strpos($parsed['path'], $bucketPattern);

        if ($pos === false) {
            throw new RuntimeException("URL не содержит бакет в пути: $url");
        }

        $cloudFileName = substr($parsed['path'], $pos + strlen($bucketPattern));

        // Проверяем объект (аналог StatObject)
        try {
            $head = $this->minioClient->headObject([
                'Bucket' => $this->bucket,
                'Key' => $cloudFileName,
            ]);
        } catch (Aws\Exception\AwsException $e) {
            throw new RuntimeException("Файл не найден: " . $e->getMessage());
        }

        // Получаем объект и передаем содержимое в callback
        $result = $this->minioClient->getObject([
            'Bucket' => $this->bucket,
            'Key' => $cloudFileName,
        ]);

        $body = $result['Body']; // Это Psr\Http\Message\StreamInterface

        // Передаем stream в callback
        $callback($body);
    }

    // Загрузить файл: поток + имя файла в облаке. Возвращает URL
    public function uploadFile($fileStream, string $cloudFileName): string
    {
        if (empty($cloudFileName)) {
            throw new InvalidArgumentException('Имя файла для загрузки в облако не может быть пустым');
        }

        if (!$fileStream instanceof StreamInterface && !is_resource($fileStream) && !is_string($fileStream)) {
            throw new InvalidArgumentException('Параметр $fileStream должен быть ресурсом, PSR-7 StreamInterface или строкой');
        }

        // Загружаем в Minio
        $this->minioClient->putObject([
            'Bucket' => $this->bucket,
            'Key' => $cloudFileName,
            'Body' => $fileStream,
            'ContentType' => self::MINIO_CONTENT_TYPE,
        ]);

        // Формируем URL файла (пример, зависит от вашей конфигурации эндпоинта)
        $endpoint = $this->minioClient->getEndpoint();

        // Обычно URL выглядит как http(s)://endpoint/bucket/key
        // В S3 SDK PHP getEndpoint возвращает Guzzle Uri объект
        $url = (string) $endpoint . '/' . $this->bucket . '/' . $cloudFileName;

        return $url;
    }
}

*/


/*
use Psr\Container\ContainerInterface;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class MinioStorageConfig
{
    public string $address;
    public int $port;
    public string $login;
    public string $password;
    public bool $ssl;
    public ?string $region;

    public function __construct(string $address, int $port, string $login, string $password, bool $ssl, ?string $region = null)
    {
        $this->address = $address;
        $this->port = $port;
        $this->login = $login;
        $this->password = $password;
        $this->ssl = $ssl;
        $this->region = $region;
    }
}

interface ICloudStorage
{
    // Определите методы интерфейса, например:
    public function putObject(string $key, string $content): void;
    public function getObject(string $key): string;
}

class MinioStorage implements ICloudStorage
{
    private S3Client $client;
    private string $bucket;

    public function __construct(S3Client $client, string $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    public function putObject(string $key, string $content): void
    {
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $content,
        ]);
    }

    public function getObject(string $key): string
    {
        $result = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
        return (string)$result['Body'];
    }
}


// Функция для регистрации Minio клиента в DI контейнер
function addMinio(ContainerInterface $container, MinioStorageConfig $config, string $bucket): void
{
    if (empty($bucket)) {
        throw new InvalidArgumentException('Bucket name cannot be empty');
    }

    $container->set(ICloudStorage::class, function () use ($config, $bucket) {

        $scheme = $config->ssl ? 'https' : 'http';
        $endpoint = sprintf('%s://%s:%d', $scheme, $config->address, $config->port);

        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $config->region ?? 'us-east-1', // Minio не всегда требует регион, но AWS SDK требует
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true, // важная настройка для Minio
            'credentials' => [
                'key' => $config->login,
                'secret' => $config->password,
            ]
        ]);

        return new MinioStorage($s3Client, $bucket);
    });
}

*/












/*
require 'vendor/autoload.php';

$minioClient = new MinioClient([
    'endpoint' => 'play.min.io', // Адрес Minio сервера
    'accessKey' => 'Q3AM3UQ867SPQQA43P2F',
    'secretKey' => 'zuf+tfteSlswRu7BJ86wekitnifILbZam1KYY3TG',
    'usePathStyleEndpoint' => true,
]);

$bucket = 'mybucket';
$storage = new MinioStorage($minioClient, $bucket);

// Пример загрузки файла
$stream = fopen('/path/to/file', 'r');
$url = $storage->uploadFile($stream, 'filename.txt');
fclose($stream);

echo "File uploaded: $url\n";

// Пример скачивания файла
$storage->downloadFile($url, function ($streamResource) {
    // $streamResource - resource с данными файла
    file_put_contents('/tmp/downloaded_file', $streamResource);
});*/


/*
// отправка - начало -
$brokerConfigOut = new BrokerConfig(
    "rabbitmq-test.dev.gkm",
    5672,
    "esb_user",
    "~g3)s6Vevz;n47",
    ["DatareonDemoOut"]
);

$sendMessageThread = function () use ($brokerConfigOut) {
    $messageBody = [
        "id" => 1000001,
        "Name" => "Мурад Алиев",
        "Content" => ["siteId" => "composer_demo"]
    ];

    $sender = new RabbitMqMessageSender($brokerConfigOut);

    $sender->send(
        "DemoApp",
        "Datareon",
        $messageBody,
        EsbMessageFormat::Json,
        uniqid()
    );

    echo date('Y-m-d H:i:s') . " - сообщение отправлено\n";
};

$sendMessageThread();
// отправка - конец -
*/

/*
// Получение - начало -
$brokerConfigIn = new BrokerConfig(
    "rabbitmq-test.dev.gkm",
    5672,
    "esb_user",
    "~g3)s6Vevz;n47",
    ["DatareonDemoIn"]
);
// Создание экземпляра слушателя
$rabbitMqListener = new RabbitMqListener($brokerConfigIn);

// Подписка на событие OnReceived
$rabbitMqListener->onReceived(function ($sender, $message, $properties) {
    echo date('Y-m-d H:i:s') . " - получено новое сообщение\n";
    echo "Сообщение: " . json_encode($message) . "\n";
    echo "Свойства сообщения: " . json_encode($properties) . "\n";


    echo date('Y-m-d H:i:s') . " - получено новое сообщение\n";
    echo "Источник:\t{$message->sourceAppName}\tПолучатель:\t{$message->targetAppName}\t\tCorrelationId:\t{$message->correlationId}\n";
    echo "Создано:\t{$message->createdTime}\n";
    echo "Формат:\t\t{$message->messageFormat}\n";
    echo "Свойства сообщения:\n";

    foreach ($properties as $key => $value) {
        echo "$key\t$value\n";
    }

    echo "Сообщение:\n";
    echo $message->body . "\n\n\n";

    // Преобразуем массив байтов обратно в строку
    $jsonString = implode(array_map("chr", $message->body));

    // Декодируем JSON обратно в массив
    $decodedArray = json_decode($jsonString, true);


    // Проверка результата
    print_r($decodedArray);

    // $body = $message->body;

});

// Запуск слушателя
$rabbitMqListener->run('DatareonDemoIn');
// Получение - конец -
*/


echo "End \n" ;