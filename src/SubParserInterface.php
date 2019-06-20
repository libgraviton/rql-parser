<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\Exception\SyntaxErrorException;

interface SubParserInterface
{
    /**
     * @param TokenStream $tokenStream
     * @return mixed
     * @throws SyntaxErrorException
     */
    public function parse(TokenStream $tokenStream);
}
