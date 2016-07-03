<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;

class RqlOperatorSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/[a-z]\w*(?=\()/Ai', $code, $matches, null, $cursor)) {
            return null;
        }

        return new Token(
            Token::T_OPERATOR,
            $matches[0],
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}
