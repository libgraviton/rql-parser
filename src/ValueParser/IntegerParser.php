<?php
namespace Graviton\RqlParser\ValueParser;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\SubParserInterface;

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
