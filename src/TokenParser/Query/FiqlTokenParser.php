<?php
namespace Mrix\Rql\Parser\TokenParser\Query;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\TokenParserInterface;
use Mrix\Rql\Parser\TokenParser\QueryTokenParser;
use Mrix\Rql\Parser\Node\AbstractQueryNode;
use Mrix\Rql\Parser\Exception\UnknownOperatorException;

/**
 */
class FiqlTokenParser implements TokenParserInterface
{
    /**
     * @var AbstractQueryOperatorTokenParser[]
     */
    protected $operatorParsers = [];
    /**
     * @var QueryTokenParser
     */
    protected $queryTokenParser;

    /**
     * @param QueryTokenParser $queryTokenParser
     */
    public function __construct(QueryTokenParser $queryTokenParser)
    {
        $this->queryTokenParser = $queryTokenParser;
    }

    /**
     * @inheritdoc
     * @return AbstractQueryNode
     */
    public function parse(TokenStream $tokenStream)
    {
        return $this->getOperatorParser($tokenStream->lookAhead(1)->getValue())->parse($tokenStream);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_STRING) && $tokenStream->lookAhead()->test(Token::T_OPERATOR, [
            'eq', '=', '==',
            'ne', '<>', '!=',
            'lt', '<',
            'le', '<=',
            'gt', '>',
            'ge', '>=',
        ]);
    }

    /**
     * @param string $operator
     * @return AbstractQueryOperatorTokenParser
     */
    public function getOperatorParser($operator)
    {
        if (!isset($this->operatorParsers[$operator])) {
            $this->operatorParsers[$operator] = $this->createOperatorParser($operator);
        }

        return $this->operatorParsers[$operator];
    }

    /**
     * @param string $operator
     * @return AbstractQueryOperatorTokenParser
     * @throws UnknownOperatorException
     */
    protected function createOperatorParser($operator)
    {
        static $operatorMap = [
            'eq'    => FiqlOperator\EqTokenParser::class,
            '='     => FiqlOperator\EqTokenParser::class,
            '=='    => FiqlOperator\EqTokenParser::class,

            'ne'    => FiqlOperator\NeTokenParser::class,
            '<>'    => FiqlOperator\NeTokenParser::class,
            '!='    => FiqlOperator\NeTokenParser::class,

            'lt'    => FiqlOperator\LtTokenParser::class,
            '<'     => FiqlOperator\LtTokenParser::class,

            'gt'    => FiqlOperator\GtTokenParser::class,
            '>'     => FiqlOperator\GtTokenParser::class,

            'le'    => FiqlOperator\LeTokenParser::class,
            '<='    => FiqlOperator\LeTokenParser::class,

            'ge'    => FiqlOperator\GeTokenParser::class,
            '>='    => FiqlOperator\GeTokenParser::class,
        ];

        if (!isset($operatorMap[$operator])) {
            throw new UnknownOperatorException(sprintf('Unknown operator "%s"', $operator));
        }

        $className = $operatorMap[$operator];
        return new $className($this->queryTokenParser);
    }
}
