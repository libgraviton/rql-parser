<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\NodeParser\Query\AbstractComparisonOperatorNodeParser;

abstract class AbstractComparisonRqlNodeParser extends AbstractComparisonOperatorNodeParser
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $this->fieldNameParser->parse($tokenStream);
        $tokenStream->expect(Token::T_COMMA);
        $value = $this->valueParser->parse($tokenStream);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->createNode($field, $value);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, $this->getOperatorName());
    }
}
