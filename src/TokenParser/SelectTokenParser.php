<?php
namespace Mrix\Rql\Parser\TokenParser;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\AbstractTokenParser;
use Mrix\Rql\Parser\Node\SelectNode;

/**
 */
class SelectTokenParser extends AbstractTokenParser
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_SELECT_OPERATOR);
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $fields[] = $tokenStream->expect(Token::T_STRING)->getValue();
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SelectNode($fields);
    }

    /**
     * @inheritdoc
     */
    public function supports(Token $token)
    {
        return $token->test(Token::T_SELECT_OPERATOR);
    }
}
