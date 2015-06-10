<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Fiql;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\TokenParser\Query\AbstractFiqlTokenParser;
use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;

/**
 */
abstract class AbstractArrayOperatorTokenParser extends AbstractFiqlTokenParser
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
        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorNames());
        $values = $this->getParser()->getExpressionParser()->parseArray($tokenStream);

        return $this->createNode($field, $values);
    }
}
