<?php
namespace Graviton\RqlParser\SubLexer;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerInterface;

class StringSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/([a-z0-9]|\%[0-9a-f]{2})+/Ai', $code, $matches, 0, $cursor)) {
            return null;
        } elseif (ctype_digit($matches[0])) {
            return null;
        }

        return new Token(
            Token::T_STRING,
            rawurldecode($matches[0]),
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}
