<?php
namespace Graviton\RqlParser\TypeCaster;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TypeCasterInterface;

class FloatTypeCaster implements TypeCasterInterface
{
    /**
     * @inheritdoc
     */
    public function typeCast(Token $token)
    {
        if ($token->test(Token::T_NULL)) {
            return 0.;
        } elseif ($token->test(Token::T_TRUE)) {
            return 1.;
        } elseif ($token->test(Token::T_FALSE)) {
            return 0.;
        } elseif ($token->test(Token::T_EMPTY)) {
            return 0.;
        } elseif ($token->test(Token::T_DATE)) {
            return (float)(new \DateTime($token->getValue()))->format('YmdHis');
        } else {
            return (float)$token->getValue();
        }
    }
}
