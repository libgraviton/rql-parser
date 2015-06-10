<?php
namespace Xiag\Rql\Parser;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;

/**
 */
interface TokenParserInterface
{
    /**
     * @param Parser $parser
     * @return void
     */
    public function setParser(Parser $parser);

    /**
     * @param TokenStream $tokenStream
     * @return AbstractNode
     * @throws SyntaxErrorException
     */
    public function parse(TokenStream $tokenStream);

    /**
     * @param TokenStream $tokenStream
     * @return bool
     */
    public function supports(TokenStream $tokenStream);
}
