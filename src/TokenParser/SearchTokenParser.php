<?php

namespace Xiag\Rql\Parser\TokenParser;


use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\AbstractTokenParser;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\Node\SearchNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;

class SearchTokenParser extends AbstractTokenParser
{

    public function parse(TokenStream $tokenStream)
    {
        $searchTerms = [];

        $tokenStream->expect(Token::T_OPERATOR, 'search');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $searchTermsImploded = $tokenStream->expect(Token::T_STRING)->getValue();
        $searchTerms[] = explode(" ", $searchTermsImploded);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SearchNode($searchTerms);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'search');
    }


}