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
        //define matches for safety sake
        $matches = [''];

        if (preg_match('/ *\\& */A', $code, $matches, null, $cursor)) {
            return new Token(Token::T_AMPERSAND, $matches[0], $cursor, $cursor + strlen($matches[0]));
        } elseif (preg_match('/ *\\| */A', $code, $matches, null, $cursor)) {
            return new Token(Token::T_VERTICAL_BAR, $matches[0], $cursor, $cursor + strlen($matches[0]));
        } elseif (preg_match('/ *\\, */A', $code, $matches, null, $cursor)) {
            return new Token(Token::T_COMMA, $matches[0], $cursor, $cursor + strlen($matches[0]));
        } elseif (preg_match('/ *\\( */A', $code, $matches, null, $cursor)) {
            return new Token(Token::T_OPEN_PARENTHESIS, $matches[0], $cursor, $cursor + strlen($matches[0]));
        } elseif (preg_match('/ *\\) */A', $code, $matches, null, $cursor)) {
            return new Token(Token::T_CLOSE_PARENTHESIS, $matches[0], $cursor, $cursor + strlen($matches[0]));
        } elseif (preg_match('/ *\\: */A', $code, $matches, null, $cursor)) {
            return new Token(Token::T_COLON, $matches[0], $cursor, $cursor + strlen($matches[0]));
        } else {
            return null;
        }
    }
}
