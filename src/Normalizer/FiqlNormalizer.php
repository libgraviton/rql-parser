<?php
namespace Mrix\Rql\Parser\Normalizer;

use Mrix\Rql\Parser\NormalizerInterface;

/**
 */
class FiqlNormalizer implements NormalizerInterface
{
    const REGEX_STRING      = '(?:\w|\-|\+|\:|\*|\$|\.|\%[0-9a-f]{2})+';
    const REGEX_OPERATOR    = '=[a-z]\w*=';

    /**
     * @inheritdoc
     */
    public function normalize($rql)
    {
        $rql = strtr($rql, [
            '=='    => '=eq=',
            '='     => '=eq=',

            '!='    => '=ne=',
            '<>'    => '=ne=',

            '>='    => '=ge=',
            '<='    => '=le=',

            '>'     => '=gt=',
            '<'     => '=lt=',
        ]);

        return preg_replace_callback(
            sprintf(
                '/(?P<property>%s|\(%s\))(?P<operator>%s)(?P<value>%s|\(%s\))/',
                self::REGEX_STRING, self::REGEX_STRING,
                self::REGEX_OPERATOR,
                self::REGEX_STRING, self::REGEX_STRING
            ),
            function ($match) {
                return sprintf('%s(%s,%s)', substr($match['operator'], 1, -1), $match['property'], $match['value']);
            },
            $rql
        );
    }
}
