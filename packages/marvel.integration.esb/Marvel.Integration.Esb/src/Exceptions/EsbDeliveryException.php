<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb\Exceptions;

class EsbDeliveryException extends \Exception
{
    private $correlationId;

    public function __construct($message, $correlationId = null) {
        parent::__construct($message);
        $this->correlationId = $correlationId ?? '';
    }

    public function getCorrelationId() {
        return $this->correlationId;
    }
}
