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
class OperatorTokenParser implements TokenParserInterface
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
        return $this->getOperatorParser($tokenStream->getCurrent()->getValue())->parse($tokenStream);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, [
            'eq', 'ne', 'lt', 'gt', 'lte', 'gte',
            'in', 'out',
            'and', 'or', 'not',
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
            'eq'        => ScalarOperator\EqTokenParser::class,
            'ne'        => ScalarOperator\NeTokenParser::class,
            'lt'        => ScalarOperator\LtTokenParser::class,
            'gt'        => ScalarOperator\GtTokenParser::class,
            'lte'       => ScalarOperator\LteTokenParser::class,
            'gte'       => ScalarOperator\GteTokenParser::class,

            'in'        => ArrayOperator\InTokenParser::class,
            'out'       => ArrayOperator\OutTokenParser::class,

            'and'       => LogicOperator\AndTokenParser::class,
            'or'        => LogicOperator\OrTokenParser::class,
            'not'       => LogicOperator\NotTokenParser::class,
        ];

        if (!isset($operatorMap[$operator])) {
            throw new UnknownOperatorException(sprintf('Unknown operator "%s"', $operator));
        }

        $className = $operatorMap[$operator];
        return new $className($this->queryTokenParser);
    }
}
