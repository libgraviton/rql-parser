<?php
namespace Xiag\Rql\Parser\DataType;

/**
 */
class DateTime extends \DateTime
{
    /**
     * @param string $dateTime
     * @return static
     */
    public static function createFromRqlFormat($dateTime)
    {
        if (strlen($dateTime) === 20) {
            $dateTime = strtr($dateTime, ['Z' => 'UTC']);
        } elseif (strlen($dateTime) === 10) {
            $dateTime = $dateTime . 'T00:00:00UTC';
        }

        return new static($dateTime, new \DateTimeZone('UTC'));
    }
}
