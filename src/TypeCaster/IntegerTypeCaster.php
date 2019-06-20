<?php
namespace Graviton\RqlParser\TypeCaster;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TypeCasterInterface;

class IntegerTypeCaster implements TypeCasterInterface
{
    /**
     * @inheritdoc
     */
    public function typeCast(Token $token)
    {
        if ($token->test(Token::T_NULL)) {
            return 0;
        } elseif ($token->test(Token::T_TRUE)) {
            return 1;
        } elseif ($token->test(Token::T_FALSE)) {
            return 0;
        } elseif ($token->test(Token::T_EMPTY)) {
            return 0;
        } elseif ($token->test(Token::T_DATE)) {
            return (int)(new \DateTime($token->getValue()))->format('YmdHis');
        } else {
            return (int)$token->getValue();
        }
    }
}
