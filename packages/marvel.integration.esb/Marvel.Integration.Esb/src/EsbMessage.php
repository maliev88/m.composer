<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb;

use DateTimeImmutable;
use InvalidArgumentException;
use DateTimeInterface;

class EsbMessage implements EsbMessageInterface
{
    private string $sourceAppName;
    private string $targetAppName;
    private object $messageFormat;
    private ?string $correlationId;
    private DateTimeImmutable $createdTime;
    private array $body;

    public function __construct(string $sourceAppName, string $targetAppName, ?string $correlationId, DateTimeImmutable $createdTime, array $body, EsbMessageFormat $messageFormat)
    {
        if (empty($sourceAppName)) {
            throw new InvalidArgumentException('sourceAppName cannot be null or empty.');
        }
        if (empty($targetAppName)) {
            throw new InvalidArgumentException('targetAppName cannot be null or empty.');
        }
        if (empty($body)) {
            throw new InvalidArgumentException('body cannot be null or empty.');
        }

        $this->sourceAppName = $sourceAppName;
        $this->targetAppName = $targetAppName;
        $this->correlationId = $correlationId;
        $this->createdTime = $createdTime;
        $this->body = $body;
        $this->messageFormat = $messageFormat;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedTime(): \DateTimeImmutable
    {
        return $this->createdTime;
    }

    /**
     * @inheritDoc
     */
    public function getSourceAppName(): string
    {
        return $this->sourceAppName;
    }

    /**
     * @inheritDoc
     */
    public function getTargetAppName(): string
    {
        return $this->targetAppName;
    }

    /**
     * @inheritDoc
     */
    public function getMessageFormat(): EsbMessageFormat
    {
        return EsbMessageFormat::Json;
    }

    /**
     * @inheritDoc
     */
    public function serialize(): string
    {
        return json_encode([
            'sourceAppName' => $this->sourceAppName,
            'targetAppName' => $this->targetAppName,
            'correlationId' => $this->correlationId,
            'createdTime' => $this->createdTime->format(DateTimeInterface::ATOM),
            'body' => array_values(unpack('C*', json_encode($this->body, JSON_UNESCAPED_UNICODE))),
            'messageFormat' => $this->messageFormat->name, // предположим, что EsbMessageFormat является перечислением
        ], JSON_UNESCAPED_UNICODE);
    }
}