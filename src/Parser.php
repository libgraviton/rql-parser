<?php
namespace Xiag\Rql\Parser;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;

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

            ->addTokenParser(new TokenParser\Query\Basic\LogicOperator\AndTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\Basic\LogicOperator\OrTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\Basic\LogicOperator\NotTokenParser($queryTokenParser))

            ->addTokenParser(new TokenParser\Query\Basic\ArrayOperator\InTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ArrayOperator\OutTokenParser())

            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\EqTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\NeTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\LtTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\GtTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\LeTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\GeTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\LikeTokenParser())

            ->addTokenParser(new TokenParser\Query\Fiql\ArrayOperator\InTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ArrayOperator\OutTokenParser())

            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\EqTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\NeTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\LtTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\GtTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\LeTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\GeTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\LikeTokenParser());

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
