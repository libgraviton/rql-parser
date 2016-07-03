<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;

class NumberSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/[-+]?[0-9]*\.?[0-9]+(?:[eE][-+]?[0-9]+)?/A', $code, $matches, null, $cursor)) {
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
