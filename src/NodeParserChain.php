<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\Exception\SyntaxErrorException;

class NodeParserChain implements NodeParserInterface
{
    /**
     * @var NodeParserInterface[]
     */
    protected $nodeParsers;

    /**
     * @param NodeParserInterface $nodeParser
     * @return $this
     */
    public function addNodeParser(NodeParserInterface $nodeParser)
    {
        $this->nodeParsers[] = $nodeParser;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        foreach ($this->nodeParsers as $nodeParser) {
            if ($nodeParser->supports($tokenStream)) {
                return $nodeParser->parse($tokenStream);
            }
        }

        throw new SyntaxErrorException(
            sprintf(
                'Unexpected token "%s" (%s) at position %d',
                $tokenStream->getCurrent()->getValue(),
                $tokenStream->getCurrent()->getName(),
                $tokenStream->getCurrent()->getStart()
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        foreach ($this->nodeParsers as $nodeParser) {
            if ($nodeParser->supports($tokenStream)) {
                return true;
            }
        }

        return false;
    }
}
