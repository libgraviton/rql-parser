<?php
namespace Xiag\Rql\Parser\TypeCaster;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TypeCasterInterface;

/**
 * String type caster
 */
class StringTypeCaster implements TypeCasterInterface
{
    /**
     * @inheritdoc
     */
    public function typeCast(Token $token)
    {
        if ($token->test(Token::T_NULL)) {
            return 'null';
        } elseif ($token->test(Token::T_TRUE)) {
            return 'true';
        } elseif ($token->test(Token::T_FALSE)) {
            return 'false';
        } elseif ($token->test(Token::T_EMPTY)) {
            return '';
        } elseif ($token->test(Token::T_GLOB)) {
            return rawurldecode($token->getValue());
        } else {
            return $token->getValue();
        }
    }
}
