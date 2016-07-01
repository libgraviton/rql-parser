<?php
namespace Xiag\Rql\Parser;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;

interface SubParserInterface
{
    /**
     * @param TokenStream $tokenStream
     * @return mixed
     * @throws SyntaxErrorException
     */
    public function parse(TokenStream $tokenStream);
}
