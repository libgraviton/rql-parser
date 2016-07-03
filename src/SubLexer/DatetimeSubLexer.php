<?php
namespace Xiag\Rql\Parser\SubLexer;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\SubLexerInterface;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;

class DatetimeSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        $regExp = '/(?<y>\d{4})-(?<m>\d{2})-(?<d>\d{2})T(?<h>\d{2}):(?<i>\d{2}):(?<s>\d{2})Z/A';
        if (!preg_match($regExp, $code, $matches, null, $cursor)) {
            return null;
        }

        if (
            !checkdate($matches['m'], $matches['d'], $matches['y']) ||
            !($matches['h'] < 24 && $matches['i'] < 60 && $matches['s'] < 60)
        ) {
            throw new SyntaxErrorException(sprintf('Invalid datetime value "%s"', $matches[0]));
        }

        return new Token(
            Token::T_DATE,
            $matches[0],
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}
