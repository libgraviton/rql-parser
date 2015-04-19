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
        if (strlen($dateTime) === 20) {
            $dateTime = strtr($dateTime, ['Z' => 'UTC']);
        }

        return new static($dateTime, new \DateTimeZone('UTC'));
    }
}
