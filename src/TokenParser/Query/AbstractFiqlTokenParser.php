<?php
namespace Mrix\Rql\Parser\TokenParser\Query;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\AbstractTokenParser;
use Mrix\Rql\Parser\Node\AbstractQueryNode;

/**
 */
abstract class AbstractFiqlTokenParser extends AbstractTokenParser
{
    /**
     * @param string $field
     * @param mixed $value
     * @return AbstractQueryNode
     */
    abstract protected function createNode($field, $value);

    /**
     * @return array
     */
    abstract protected function getOperatorNames();

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorNames());
        $value = $this->getParser()->getExpressionParser()->parseScalar($tokenStream);

        return $this->createNode($field, $value);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_STRING) &&
            $tokenStream->lookAhead()->test(Token::T_OPERATOR, $this->getOperatorNames());
    }
}
