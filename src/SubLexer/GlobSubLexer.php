<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;
use Xiag\Rql\Parser\Glob;

class GlobSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/([a-z0-9\*\?._]|\%[0-9a-f]{2})+/Ai', $code, $matches, null, $cursor)) {
            return null;
        } elseif (strpos($matches[0], '?') === false && strpos($matches[0], '*') === false) {
            return null;
        }

        return new Token(
            Token::T_GLOB,
            $this->decodeGlob($matches[0]),
            $cursor,
            $cursor + strlen($matches[0])
        );
    }

    private function decodeGlob($glob)
    {
        return preg_replace_callback(
            '/[^\*\?]+/i',
            function ($encoded) {
                return Glob::encode(rawurldecode($encoded[0]));
            },
            $glob
        );
    }
}
