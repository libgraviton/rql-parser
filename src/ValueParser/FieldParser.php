<?php
namespace Graviton\RqlParser\ValueParser;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\SubParserInterface;

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
