<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;

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
