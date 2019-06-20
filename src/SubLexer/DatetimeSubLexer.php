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
        $regExp = '/(?<y>\d{4})-(?<m>\d{2})-(?<d>\d{2})T(?<h>\d{2}):(?<i>\d{2}):(?<s>\d{2})(?<tz>(Z|[\+|-]\d{4}))/A';
        if (!preg_match($regExp, $code, $matches, null, $cursor)) {
            return null;
        }

        if (!checkdate($matches['m'], $matches['d'], $matches['y']) ||
            !($matches['h'] < 24 && $matches['i'] < 60 && $matches['s'] < 60)) {
            throw new SyntaxErrorException(sprintf('Invalid datetime value "%s"', $matches[0]));
        }

        $valueLength = strlen($matches[0]);

        // ensure "proper" timezone type
        if (substr($matches[0], -1) == 'Z') {
            $matches[0] = substr($matches[0], 0, -1) . '+0000';
        }

        return new Token(
            Token::T_DATE,
            $matches[0],
            $cursor,
            $cursor + $valueLength
        );
    }
}
