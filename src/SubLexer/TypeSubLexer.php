<?php
namespace Graviton\RqlParser\SubLexer;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerInterface;

class TypeSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/[a-z]\w*(?=:)/Ai', $code, $matches, 0, $cursor)) {
            return null;
        }

        return new Token(
            Token::T_TYPE,
            $matches[0],
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}
