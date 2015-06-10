<?php
namespace Xiag\Rql\Parser\TokenParser\Query;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\AbstractTokenParser;

/**
 */
abstract class AbstractFiqlTokenParser extends AbstractTokenParser
{
    /**
     * @return array
     */
    abstract protected function getOperatorNames();

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_STRING) &&
            $tokenStream->lookAhead()->test(Token::T_OPERATOR, $this->getOperatorNames());
    }
}
