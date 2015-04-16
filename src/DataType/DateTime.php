<?php
namespace Mrix\Rql\Parser\DataType;

/**
 */
class DateTime extends \DateTimeImmutable
{
    /**
     * @param string $dateTime
     * @return static
     */
    public static function createFromRqlFormat($dateTime)
    {
        return (new static($dateTime))->setTimezone(new \DateTimeZone('UTC'));
    }
}
