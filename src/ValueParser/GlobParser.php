<?php
namespace Xiag\Rql\Parser\ValueParser;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\DataType\Glob;

class GlobParser implements SubParserInterface
{
    /**
     * @var array Allowed types to convert to glob
     */
    protected static $allowedTypes = [
        Token::T_INTEGER,
        Token::T_FLOAT,
        Token::T_STRING,
        Token::T_DATE,
    ];

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        if ($tokenStream->test(Token::T_GLOB)) {
            return new Glob($tokenStream->next()->getValue());
        }

        $tokenStream->nextIf(Token::T_TYPE, 'glob');
        return new Glob(rawurlencode($tokenStream->expect(static::$allowedTypes)->getValue()));
    }
}
