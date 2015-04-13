<?php
namespace Mrix\Rql\Parser\TokenParser\Query;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\TokenParser\QueryTokenParser;
use Mrix\Rql\Parser\Node\Query\AbstractLogicQueryNode;

/**
 */
abstract class AbstractLogicQueryTokenParser extends QueryTokenParser
{
    /**
     * @return string
     */
    abstract protected function getOperatorName();

    /**
     * @param array $queries
     * @return AbstractLogicQueryNode
     */
    abstract protected function createNode(array $queries);

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_QUERY_OPERATOR, $this->getOperatorName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $queries = [];
        do {
            $queries[] = parent::parse($tokenStream);
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
    public function supports(Token $token)
    {
        return $token->test(Token::T_QUERY_OPERATOR, $this->getOperatorName());
    }
}
