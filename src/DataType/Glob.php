<?php
namespace Xiag\Rql\Parser\DataType;

/**
 */
class Glob
{
    const REPLACE_ASTERISK = '___xiag_re_asterisk_3_1415926535_8979323846___';
    const REPLACE_QUESTION = '___xiag_re_question_3_1415926535_8979323846___';

    /**
     * @var string
     */
    protected $glob;

    /**
     * @param string $glob
     */
    public function __construct($glob)
    {
        $this->glob = $glob;
    }

    /**
     * @return string
     */
    public function toRegex()
    {
        $glob = $this->glob;

        $anchorStart = true;
        if (substr($this->glob[0], 0, 1) === '*') {
            $anchorStart = false;
            $glob = ltrim($glob, '*');
        }

        $anchorEnd = true;
        if (substr($this->glob[0], -1) === '*') {
            $anchorEnd = false;
            $glob = rtrim($glob, '*');
        }

        $regex = strtr(
            preg_quote(rawurldecode(strtr($glob, ['*' => self::REPLACE_ASTERISK, '?' => self::REPLACE_QUESTION])), '/'),
            [self::REPLACE_ASTERISK => '.*', self::REPLACE_QUESTION => '.']
        );

        if ($anchorStart) {
            $regex = '^' . $regex;
        }
        if ($anchorEnd) {
            $regex = $regex . '$';
        }

        return '/' . $regex . '/i';
    }
}
