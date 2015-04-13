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
     * @param Token $token
     * @return bool
     */
    public function supports(Token $token);
}
