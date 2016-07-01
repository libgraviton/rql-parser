<?php
namespace Xiag\Rql\Parser\NodeParser\Query;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Node\Query\AbstractLogicalOperatorNode;

abstract class AbstractLogicalOperatorNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $conditionParser;

    /**
     * @param SubParserInterface $conditionParser
     */
    public function __construct(SubParserInterface $conditionParser)
    {
        $this->conditionParser = $conditionParser;
    }

    /**
     * @param array $queries
     * @return AbstractLogicalOperatorNode
     */
    abstract protected function createNode(array $queries);

    /**
     * @return string
     */
    abstract protected function getOperatorName();

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $queries = [];
        do {
            $queries[] = $this->conditionParser->parse($tokenStream);
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->createNode($queries);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, $this->getOperatorName());
    }
}
