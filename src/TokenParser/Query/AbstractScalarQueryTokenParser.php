<?php
namespace Mrix\Rql\Parser\TokenParser\Query;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\AbstractTokenParser;
use Mrix\Rql\Parser\Node\Query\AbstractScalarQueryNode;

/**
 */
abstract class AbstractScalarQueryTokenParser extends AbstractTokenParser
{
    /**
     * @return string
     */
    abstract protected function getOperatorName();

    /**
     * @param string $field
     * @param mixed $value
     * @return AbstractScalarQueryNode
     */
    abstract protected function createNode($field, $value);

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_QUERY_OPERATOR, $this->getOperatorName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_COMMA);
        $value = $this->parseScalar($tokenStream);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->createNode($field, $value);
    }

    /**
     * @inheritdoc
     */
    public function supports(Token $token)
    {
        return $token->test(Token::T_QUERY_OPERATOR, $this->getOperatorName());
    }
}
