<?php
namespace Graviton\RqlParser\SubLexer;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerInterface;

class NumberSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/[-+]?[0-9]*\.?[0-9]+(?:[eE][-+]?[0-9]+)?/A', $code, $matches, 0, $cursor)) {
            return null;
        }

        return new Token(
            filter_var($matches[0], FILTER_VALIDATE_INT) === false ? Token::T_FLOAT : Token::T_INTEGER,
            $matches[0],
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}
