<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\UnknownTokenException;

/**
 * Token
 */
class Token
{
    const T_END                 = -1;

    const T_INTEGER             = 1;
    const T_FLOAT               = 2;
    const T_STRING              = 3;

    const T_CLOSE_PARENTHESIS   = 11;
    const T_OPEN_PARENTHESIS    = 12;
    const T_COMMA               = 13;
    const T_AMPERSAND           = 14;
    const T_VERTICAL_BAR        = 15;

    const T_TYPE                = 31;

    const T_QUERY_OPERATOR      = 41;
    const T_SORT_OPERATOR       = 42;
    const T_LIMIT_OPERATOR      = 43;
    const T_SELECT_OPERATOR     = 44;

    const T_NULL                = 51;
    const T_EMPTY               = 52;
    const T_TRUE                = 53;
    const T_FALSE               = 54;

    /**
     * @var string
     */
    protected $value;
    /**
     * @var int
     */
    protected $type;
    /**
     * @var int
     */
    protected $position;

    /**
     * @param int $type
     * @param string $value
     * @param int $position
     */
    public function __construct($type, $value, $position)
    {
        $this->type     = $type;
        $this->value    = $value;
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return sprintf('%s(%s)', $this->getName(), $this->value);
        } catch (\Exception $e) {
            return sprintf('%s(%s)', 'UNKNOWN', $this->value);
        }
    }

    /**
     * @param int $type
     * @param string|array $value
     * @return bool
     */
    public function test($type, $value = null)
    {
        if ($this->type !== $type) {
            return false;
        }
        if ($value !== null && $this->value !== $value) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::getTypeName($this->type);
    }

    /**
     * @param int $type
     * @return string
     * @throws UnknownTokenException
     */
    public static function getTypeName($type)
    {
        static $typeMap = [
            self::T_END               => 'T_END',

            self::T_INTEGER           => 'T_INTEGER',
            self::T_FLOAT             => 'T_FLOAT',
            self::T_STRING            => 'T_STRING',

            self::T_CLOSE_PARENTHESIS => 'T_CLOSE_PARENTHESIS',
            self::T_OPEN_PARENTHESIS  => 'T_OPEN_PARENTHESIS',
            self::T_COMMA             => 'T_COMMA',
            self::T_AMPERSAND         => 'T_AMPERSAND',
            self::T_VERTICAL_BAR      => 'T_VERTICAL_BAR',

            self::T_TYPE              => 'T_TYPE',

            self::T_QUERY_OPERATOR    => 'T_QUERY_OPERATOR',
            self::T_SORT_OPERATOR     => 'T_SORT_OPERATOR',
            self::T_LIMIT_OPERATOR    => 'T_LIMIT_OPERATOR',
            self::T_SELECT_OPERATOR   => 'T_SELECT_OPERATOR',

            self::T_NULL              => 'T_NULL',
            self::T_EMPTY             => 'T_EMPTY',
            self::T_TRUE              => 'T_TRUE',
            self::T_FALSE             => 'T_FALSE',
        ];

        if (!isset($typeMap[$type])) {
            throw new UnknownTokenException(sprintf('Token of type "%s" does not exist', $type));
        }

        return $typeMap[$type];
    }
}
