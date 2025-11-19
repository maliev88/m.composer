<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb;

abstract class EsbSender implements EsbSenderInterface
{
    public function __construct()
    {
    }

    public abstract function send(string $sourceAppName, string $targetAppName, array $message, EsbMessageFormat $messageFormat, string $correlationId = null): void;

    protected function createEsbMessage(string $sourceAppName, string $targetAppName, array $message, EsbMessageFormat $messageFormat, string $correlationId): string
    {
        $esbMessage = new EsbMessage($sourceAppName, $targetAppName, $correlationId, new \DateTimeImmutable(), $message, $messageFormat);

        return $esbMessage->serialize();
    }
}
