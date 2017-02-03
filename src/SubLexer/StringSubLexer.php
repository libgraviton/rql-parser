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
        //if it doesn't match a string, return null
        if (!preg_match('/([a-z0-9_.]|\%[0-9a-f]{2})+/Ai', $code, $matches, null, $cursor))
            return null;

        //if it did match a string, ensure that the same string would not match a number
        $num_token = (new NumberSubLexer())->getTokenAt($code, $cursor);
        //if we could just as well find a number here
        if($num_token instanceof Token)
        {
            $str_len = ($cursor + strlen($matches[0])) - $cursor;
            $num_len = $num_token->getEnd() - $num_token->getStart();
            // finding '2017' in '2017-01-01' is not good enough
            if($num_len >= $str_len)
                return $num_token;
        }

        return new Token(
            Token::T_STRING,
            rawurldecode($matches[0]),
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}
