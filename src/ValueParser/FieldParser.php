<?php
namespace Xiag\Rql\Parser\ValueParser;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\SubParserInterface;

class FieldParser implements SubParserInterface
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        return $tokenStream->expect(Token::T_STRING)->getValue();
    }
}
