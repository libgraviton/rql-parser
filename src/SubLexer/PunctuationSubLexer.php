<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;

class PunctuationSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        $test = substr($code, $cursor, 1);
        if ($test === '&') {
            return new Token(Token::T_AMPERSAND, $test, $cursor, $cursor + 1);
        } elseif ($test === '|') {
            return new Token(Token::T_VERTICAL_BAR, $test, $cursor, $cursor + 1);
        } elseif ($test === ',') {
            return new Token(Token::T_COMMA, $test, $cursor, $cursor + 1);
        } elseif ($test === '(') {
            return new Token(Token::T_OPEN_PARENTHESIS, $test, $cursor, $cursor + 1);
        } elseif ($test === ')') {
            return new Token(Token::T_CLOSE_PARENTHESIS, $test, $cursor, $cursor + 1);
        } elseif ($test === ':') {
            return new Token(Token::T_COLON, $test, $cursor, $cursor + 1);
        } else {
            return null;
        }
    }
}
