<?php

require_once '/vendor/autoload.php';


use Marvel\Integration\Esb;
use Marvel\Integration\Esb\EsbMessageFormat;
use Marvel\Integration\Esb\Broker\BrokerConfig;
use Marvel\Integration\Esb\Broker\RabbitMqListener;
use Marvel\Integration\Esb\Broker\RabbitMqMessageSender;

echo "Старт \n";

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

echo "End \n";