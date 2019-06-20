<?php
namespace Graviton\RqlParser;

interface NodeParserInterface extends SubParserInterface
{
    /**
     * @inheritdoc
     * @return AbstractNode
     */
    public function parse(TokenStream $tokenStream);

    /**
     * @param TokenStream $tokenStream
     * @return bool
     */
    public function supports(TokenStream $tokenStream);
}
