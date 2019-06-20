<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\ValueParser\ScalarParser;

class RqlEncoder
{

    /**
     * Encodes a value that should be a string value in rql
     *
     * @param string $value
     * @param bool   $doCastingPrefix
     *
     * @return string
     */
    public static function encode($value, $doCastingPrefix = true)
    {
        // special cases!
        if (is_string($value) && empty($value)) {
            return 'empty()';
        }

        if ($value === true) {
            return 'true()';
        }

        if ($value === false) {
            return 'false()';
        }

        if ($value === null) {
            return 'null()';
        }

        if ($value instanceof \DateTime) {
            return $value->format(ScalarParser::DATETIME_FORMAT);
        }

        $cast = '';
        if ($doCastingPrefix && is_string($value)) {
            $cast = 'string:';
        }
        if ($doCastingPrefix && is_float($value)) {
            $cast = 'float:';
        }
        if ($doCastingPrefix && is_integer($value)) {
            $cast = 'integer:';
        }

        return
            $cast.
            strtr(
                rawurlencode($value),
                [
                    '-' => '%2D',
                    '_' => '%5F',
                    '.' => '%2E',
                    '~' => '%7E',
                ]
            );
    }

    /**
     * Encodes a value that should be a field name reference
     *
     * @param string $value
     *
     * @return string
     */
    public static function encodeFieldName($value)
    {
        return self::encode($value, false);
    }

    /**
     * Encodes a value list that should be a string value in rql
     *
     * @param array $list list of values
     *
     * @return string
     */
    public static function encodeList(array $list, $doCastingPrefix = true)
    {
        return implode(
            ',',
            array_map(
                [self::class, 'encode'],
                $list,
                array_fill(0, count($list), $doCastingPrefix)
            )
        );
    }
}
