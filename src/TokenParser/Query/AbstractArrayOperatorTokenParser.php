<?php
namespace Mrix\Rql\Parser\TokenParser\Query;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\Node\Query\AbstractArrayOperatorNode;

/**
 */
abstract class AbstractArrayOperatorTokenParser extends AbstractQueryOperatorTokenParser
{
    /**
     * @param string $field
     * @param array $values
     * @return AbstractArrayOperatorNode
     */
    abstract protected function createNode($field, array $values);

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_COMMA);
        $values = $this->queryTokenParser->getExpressionParser()->parseArray($tokenStream);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->createNode($field, $values);
    }
}
