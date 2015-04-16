<?php
namespace Mrix\Rql\Parser\TypeCaster;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TypeCasterInterface;
use Mrix\Rql\Parser\DataType\DateTime;

/**
 * Integer type caster
 */
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
            return (int)DateTime::createFromRqlFormat($token->getValue())->format('YmdHis');
        } else {
            return (int)$token->getValue();
        }
    }
}
