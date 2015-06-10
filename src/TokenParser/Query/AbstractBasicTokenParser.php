<?php
namespace Xiag\Rql\Parser\TokenParser\Query;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\AbstractTokenParser;

/**
 */
abstract class AbstractBasicTokenParser extends AbstractTokenParser
{
    /**
     * @return string
     */
    abstract protected function getOperatorName();

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, $this->getOperatorName());
    }
}
