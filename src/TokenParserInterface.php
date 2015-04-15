<?php
namespace Mrix\Rql\Parser;

/**
 */
interface TokenParserInterface
{
    /**
     * @param TokenStream $tokenStream
     * @return AbstractNode
     */
    public function parse(TokenStream $tokenStream);

    /**
     * @param TokenStream $tokenStream
     * @return bool
     */
    public function supports(TokenStream $tokenStream);
}
