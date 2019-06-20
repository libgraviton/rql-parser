<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParser\Query\AbstractComparisonOperatorNodeParser;
use Graviton\RqlParser\Exception\SyntaxErrorException;

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
