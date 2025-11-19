<?php

require_once '/vendor/autoload.php';


use Marvel\Integration\Esb;
use Marvel\Integration\Esb\EsbMessageFormat;
use Marvel\Integration\Esb\Broker\BrokerConfig;
use Marvel\Integration\Esb\Broker\RabbitMqListener;
use Marvel\Integration\Esb\Broker\RabbitMqMessageSender;

echo "Старт \n";

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
    echo "Источник:\t{$message->sourceAppName}\tПолучатель:\t{$message->targetAppName}\t\tCorrelationId:\t{$message->correlationId}\n";
    echo "Создано:\t{$message->createdTime}\n";
    echo "Формат:\t\t{$message->messageFormat}\n";
    echo "Свойства сообщения:\n";

    foreach ($properties as $key => $value) {
        echo "$key\t$value\n";
    }

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
$rabbitMqListener->run('DatareonDemoIn');
// Получение - конец -

echo "End \n";