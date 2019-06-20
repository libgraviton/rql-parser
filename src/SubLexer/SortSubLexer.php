<?php
namespace Graviton\RqlParser\SubLexer;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerInterface;

class SortSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        $test = substr($code, $cursor, 1);
        if ($test === '-') {
            return new Token(Token::T_MINUS, '-', $cursor, $cursor + 1);
        } elseif ($test === '+') {
            return new Token(Token::T_PLUS, '+', $cursor, $cursor + 1);
        } else {
            return null;
        }
    }
}
