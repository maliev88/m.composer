<?php

declare(strict_types=1);

namespace Marvel\Integration\Esb;

/**
 * Возможные варианты формата передаваемого сообщения.
 */
enum EsbMessageFormat: string
{
    case Json = 'Json';
    case Xml = 'Xml';
}