<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;

/**
 * Parser
 */
class Parser
{
    /**
     * @var TokenParserGroup
     */
    protected $tokenParserGroup;
    /**
     * @var ExpressionParserInterface
     */
    protected $expressionParser;

    /**
     * @param ExpressionParserInterface $expressionParser
     */
    public function __construct(ExpressionParserInterface $expressionParser)
    {
        $this->expressionParser = $expressionParser;

        $this->tokenParserGroup = new TokenParserGroup();
        $this->tokenParserGroup->setParser($this);
    }

    /**
     * @return ExpressionParserInterface
     */
    public function getExpressionParser()
    {
        return $this->expressionParser;
    }

    /**
     * @param TokenParserInterface $tokenParser
     * @return $this
     */
    public function addTokenParser(TokenParserInterface $tokenParser)
    {
        $this->tokenParserGroup->addTokenParser($tokenParser);

        return $this;
    }

    /**
     * @return Parser
     */
    public static function createDefault()
    {
        $queryTokenParser = new TokenParserGroup();
        $queryTokenParser
            ->addTokenParser(new TokenParser\Query\GroupTokenParser($queryTokenParser))

            ->addTokenParser(new TokenParser\Query\LogicOperator\AndTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\LogicOperator\OrTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\LogicOperator\NotTokenParser($queryTokenParser))

            ->addTokenParser(new TokenParser\Query\ArrayOperator\InTokenParser())
            ->addTokenParser(new TokenParser\Query\ArrayOperator\OutTokenParser())

            ->addTokenParser(new TokenParser\Query\ScalarOperator\EqTokenParser())
            ->addTokenParser(new TokenParser\Query\ScalarOperator\NeTokenParser())
            ->addTokenParser(new TokenParser\Query\ScalarOperator\LtTokenParser())
            ->addTokenParser(new TokenParser\Query\ScalarOperator\GtTokenParser())
            ->addTokenParser(new TokenParser\Query\ScalarOperator\LeTokenParser())
            ->addTokenParser(new TokenParser\Query\ScalarOperator\GeTokenParser())

            ->addTokenParser(new TokenParser\Query\FiqlOperator\EqTokenParser())
            ->addTokenParser(new TokenParser\Query\FiqlOperator\NeTokenParser())
            ->addTokenParser(new TokenParser\Query\FiqlOperator\LtTokenParser())
            ->addTokenParser(new TokenParser\Query\FiqlOperator\GtTokenParser())
            ->addTokenParser(new TokenParser\Query\FiqlOperator\LeTokenParser())
            ->addTokenParser(new TokenParser\Query\FiqlOperator\GeTokenParser());

        return (new self(
            (new ExpressionParser())
                ->registerTypeCaster('string', new TypeCaster\StringTypeCaster())
                ->registerTypeCaster('integer', new TypeCaster\IntegerTypeCaster())
                ->registerTypeCaster('float', new TypeCaster\FloatTypeCaster())
                ->registerTypeCaster('boolean', new TypeCaster\BooleanTypeCaster())
        ))
            ->addTokenParser(new TokenParser\SelectTokenParser())
            ->addTokenParser($queryTokenParser)
            ->addTokenParser(new TokenParser\SortTokenParser())
            ->addTokenParser(new TokenParser\LimitTokenParser());
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
            $queryBuilder->addNode($this->tokenParserGroup->parse($tokenStream));
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
