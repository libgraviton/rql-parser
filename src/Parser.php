<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\NodeParser\Query\LogicalOperator;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator;

class Parser
{
    /**
     * @var NodeParserInterface
     */
    protected $nodeParser;

    /**
     * @param NodeParserInterface $nodeParser
     */
    public function __construct(NodeParserInterface $nodeParser = null)
    {
        $this->nodeParser = $nodeParser ?: static::createDefaultNodeParser();
    }

    /**
     * @param NodeParserInterface $nodeParser
     * @return $this
     * @codeCoverageIgnore
     */
    public function setNodeParser(NodeParserInterface $nodeParser)
    {
        $this->nodeParser = $nodeParser;
        return $this;
    }

    /**
     * @return NodeParserInterface
     * @codeCoverageIgnore
     */
    public function getNodeParser()
    {
        return $this->nodeParser;
    }

    /**
     * @return NodeParserInterface
     */
    public static function createDefaultNodeParser()
    {
        $scalarParser = (new ValueParser\ScalarParser())
            ->registerTypeCaster('string', new TypeCaster\StringTypeCaster())
            ->registerTypeCaster('integer', new TypeCaster\IntegerTypeCaster())
            ->registerTypeCaster('float', new TypeCaster\FloatTypeCaster())
            ->registerTypeCaster('boolean', new TypeCaster\BooleanTypeCaster());
        $arrayParser = new ValueParser\ArrayParser($scalarParser);
        $globParser = new ValueParser\GlobParser();
        $fieldParser = new ValueParser\FieldParser();
        $integerParser = new ValueParser\IntegerParser();

        $queryNodeParser = new NodeParser\QueryNodeParser();
        $queryNodeParser
            ->addNodeParser(new NodeParser\Query\GroupNodeParser($queryNodeParser))

            ->addNodeParser(new LogicalOperator\AndNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\OrNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\NotNodeParser($queryNodeParser))

            ->addNodeParser(new ComparisonOperator\Rql\InNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Rql\OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Rql\EqNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\NeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\GtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\GeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LikeNodeParser($fieldParser, $globParser))

            ->addNodeParser(new ComparisonOperator\Fiql\InNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Fiql\OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Fiql\EqNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\NeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\GtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\GeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LikeNodeParser($fieldParser, $globParser));

        return (new NodeParserChain())
            ->addNodeParser($queryNodeParser)
            ->addNodeParser(new NodeParser\SelectNodeParser($fieldParser))
            ->addNodeParser(new NodeParser\SortNodeParser($fieldParser))
            ->addNodeParser(new NodeParser\LimitNodeParser($integerParser));
    }

    /**
     * @param TokenStream $tokenStream
     * @return Query
     * @throws SyntaxErrorException
     */
    public function parse(TokenStream $tokenStream)
    {
        $queryBuilder = $this->createQueryBuilder();
        while (!$tokenStream->isEnd()) {
            $queryBuilder->addNode($this->nodeParser->parse($tokenStream));
            $tokenStream->nextIf(Token::T_AMPERSAND);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return new QueryBuilder();
    }
}
