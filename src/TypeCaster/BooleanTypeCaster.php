<?php
namespace Mrix\Rql\Parser\TypeCaster;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TypeCasterInterface;

/**
 * Boolean type caster
 */
class BooleanTypeCaster implements TypeCasterInterface
{
    /**
     * @inheritdoc
     */
    public function typeCast(Token $token)
    {
        if ($token->test(Token::T_NULL)) {
            return false;
        } elseif ($token->test(Token::T_TRUE)) {
            return true;
        } elseif ($token->test(Token::T_FALSE)) {
            return false;
        } elseif ($token->test(Token::T_EMPTY)) {
            return false;
        } else {
            return (bool)$token->getValue();
        }
    }
}
