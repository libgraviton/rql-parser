<?php
namespace Xiag\Rql\Parser\TokenParser;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\AbstractTokenParser;
use Xiag\Rql\Parser\Node\LimitNode;

/**
 */
class LimitTokenParser extends AbstractTokenParser
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $limit = null;
        $offset = null;

        $tokenStream->expect(Token::T_OPERATOR, 'limit');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $limit = (int)$tokenStream->expect(Token::T_INTEGER)->getValue();
        if ($tokenStream->nextIf(Token::T_COMMA)) {
            $offset = (int)$tokenStream->expect(Token::T_INTEGER)->getValue();
        }

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new LimitNode($limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'limit');
    }
}
