<?php
namespace Xiag\Rql\Parser\ValueParser;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\SubParserInterface;

class IntegerParser implements SubParserInterface
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        return (int)$tokenStream->expect(Token::T_INTEGER)->getValue();
    }
}
