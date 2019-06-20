<?php
namespace Graviton\RqlParser\SubLexer;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerInterface;

class ConstantSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        $test7 = substr($code, $cursor, 7);
        if ($test7 === 'empty()') {
            return new Token(Token::T_EMPTY, $test7, $cursor, $cursor + 7);
        } elseif ($test7 === 'false()') {
            return new Token(Token::T_FALSE, $test7, $cursor, $cursor + 7);
        }

        $test6 = substr($code, $cursor, 6);
        if ($test6 === 'null()') {
            return new Token(Token::T_NULL, $test6, $cursor, $cursor + 6);
        } elseif ($test6 === 'true()') {
            return new Token(Token::T_TRUE, $test6, $cursor, $cursor + 6);
        }

        return null;
    }
}
