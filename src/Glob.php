<?php
namespace Graviton\RqlParser;

class Glob
{
    /**
     * @var string
     */
    private $glob;

    /**
     * @param string $glob
     */
    public function __construct($glob)
    {
        $this->glob = $glob;
    }

    /**
     * Encode raw string value
     *
     * @param string $value
     * @return string
     */
    public static function encode($value)
    {
        return addcslashes($value, '\?*');
    }

    /**
     * Returns raw glob
     *
     * @return string
     */
    public function __toString()
    {
        return $this->glob;
    }

    /**
     * Returns RQL representation
     *
     * @return string
     */
    public function toRql()
    {
        return $this->decoder(
            '*',
            '?',
            function ($char) {
                return strtr(rawurlencode($char), [
                    '-' => '%2D',
                    '_' => '%5F',
                    '.' => '%2E',
                    '~' => '%7E',
                ]);
            }
        );
    }

    /**
     * Returns RegExp representation
     *
     * @return string
     */
    public function toRegex()
    {
        $regex = $this->decoder(
            '.*',
            '.',
            function ($char) {
                return preg_quote($char, '/');
            }
        );

        return '^' . $regex . '$';
    }

    /**
     * Returns LIKE representation
     *
     * @return string
     */
    public function toLike()
    {
        return $this->decoder(
            '%',
            '_',
            function ($char) {
                return addcslashes($char, '\%_');
            }
        );
    }

    private function decoder($many, $one, callable $escaper)
    {
        return preg_replace_callback(
            '/\\\\.|\*|\?|./',
            function ($match) use ($many, $one, $escaper) {
                if ($match[0] === '*') {
                    return $many;
                } elseif ($match[0] === '?') {
                    return $one;
                } else {
                    return $escaper(stripslashes($match[0]));
                }
            },
            $this->glob
        );
    }
}
