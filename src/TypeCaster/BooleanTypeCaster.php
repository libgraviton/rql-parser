<?php
namespace Xiag\Rql\Parser\TypeCaster;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TypeCasterInterface;

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
        } elseif ($token->test(Token::T_DATE)) {
            return $token->getValue() === '0000-00-00T00:00:00Z';
        } else {
            return (bool)$token->getValue();
        }
    }
}
