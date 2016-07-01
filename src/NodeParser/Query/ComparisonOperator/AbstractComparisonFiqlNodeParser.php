<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\NodeParser\Query\AbstractComparisonOperatorNodeParser;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;

abstract class AbstractComparisonFiqlNodeParser extends AbstractComparisonOperatorNodeParser
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $field = $this->fieldNameParser->parse($tokenStream);
        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorName());
        $value = $this->valueParser->parse($tokenStream);

        return $this->createNode($field, $value);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        try {
            $tokenStream = clone $tokenStream;
            $this->fieldNameParser->parse($tokenStream);
            return $tokenStream->test(Token::T_OPERATOR, $this->getOperatorName());
        } catch (SyntaxErrorException $e) {
            return false;
        }
    }
}
