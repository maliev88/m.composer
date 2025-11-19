<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb\Broker;

interface BrokerConfigInterface
{
    public function getHostName(): string;

    public function getPassword(): string;

    public function getPort(): int;

    public function getQueues(): array;

    public function getUserName(): string;
}