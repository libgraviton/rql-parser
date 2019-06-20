<?php
namespace Graviton\RqlParser\NodeParser;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\Node\SortNode;
use Graviton\RqlParser\SubParserInterface;

class SortNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $fieldNameParser;

    /**
     * @param SubParserInterface $fieldNameParser
     */
    public function __construct(SubParserInterface $fieldNameParser)
    {
        $this->fieldNameParser = $fieldNameParser;
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'sort');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $direction = $tokenStream->expect([Token::T_PLUS, Token::T_MINUS]);
            $fields[$this->fieldNameParser->parse($tokenStream)] = $direction->test(Token::T_PLUS) ?
                SortNode::SORT_ASC :
                SortNode::SORT_DESC;

            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SortNode($fields);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'sort');
    }
}
