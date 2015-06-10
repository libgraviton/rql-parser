<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Fiql;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\TokenParser\Query\AbstractFiqlTokenParser;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;

/**
 */
abstract class AbstractScalarOperatorTokenParser extends AbstractFiqlTokenParser
{
    /**
     * @param string $field
     * @param mixed $value
     * @return AbstractScalarOperatorNode
     */
    abstract protected function createNode($field, $value);

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
}
