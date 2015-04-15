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
    public function supports(Token $token)
    {
        return $token->test(Token::T_OPERATOR, [
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
            'eq'        => ScalarQuery\EqTokenParser::class,
            'ne'        => ScalarQuery\NeTokenParser::class,
            'lt'        => ScalarQuery\LtTokenParser::class,
            'gt'        => ScalarQuery\GtTokenParser::class,
            'lte'       => ScalarQuery\LteTokenParser::class,
            'gte'       => ScalarQuery\GteTokenParser::class,

            'in'        => ArrayQuery\InTokenParser::class,
            'out'       => ArrayQuery\OutTokenParser::class,

            'and'       => LogicQuery\AndTokenParser::class,
            'or'        => LogicQuery\OrTokenParser::class,
            'not'       => LogicQuery\NotTokenParser::class,
        ];

        if (!isset($operatorMap[$operator])) {
            throw new UnknownOperatorException(sprintf('Unknown operator "%s"', $operator));
        }

        $className = $operatorMap[$operator];
        return new $className($this->queryTokenParser);
    }
}
