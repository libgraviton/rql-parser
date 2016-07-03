<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;

class GlobSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/([a-z0-9\*\?]|\%[0-9a-f]{2})+/Ai', $code, $matches, null, $cursor)) {
            return null;
        } elseif (strpos($matches[0], '?') === false && strpos($matches[0], '*') === false) {
            return null;
        }

        return new Token(
            Token::T_GLOB,
            $matches[0],
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}
