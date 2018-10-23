<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;

class StringSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/([a-z0-9_]|\%[0-9a-f]{2})+/Ai', $code, $matches, null, $cursor)) {
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
