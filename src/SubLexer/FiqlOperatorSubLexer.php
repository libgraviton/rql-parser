<?php
namespace Graviton\RqlParser\SubLexer;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerInterface;

class FiqlOperatorSubLexer implements SubLexerInterface
{
    private static $operatorMap = [
        '='  => 'eq',
        '==' => 'eq',

        '!=' => 'ne',
        '<>' => 'ne',

        '>'  => 'gt',
        '<'  => 'lt',

        '>=' => 'ge',
        '<=' => 'le',
    ];

    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/(=[a-z]\w*=|==|!=|<>|>=|<=|<|>|==|=)/Ai', $code, $matches, 0, $cursor)) {
            return null;
        }

        return new Token(
            Token::T_OPERATOR,
            $this->getValue($matches[0]),
            $cursor,
            $cursor + strlen($matches[0])
        );
    }

    private function getValue($match)
    {
        if (isset(self::$operatorMap[$match])) {
            return self::$operatorMap[$match];
        } else {
            return substr($match, 1, -1);
        }
    }
}
