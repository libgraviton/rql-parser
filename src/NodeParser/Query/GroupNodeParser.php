<?php
namespace Graviton\RqlParser\NodeParser\Query;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\SubParserInterface;
use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\Node\Query\LogicalOperator\AndNode;
use Graviton\RqlParser\Node\Query\LogicalOperator\OrNode;

class GroupNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $conditionParser;

    /**
     * @param SubParserInterface $conditionParser
     */
    public function __construct(SubParserInterface $conditionParser)
    {
        $this->conditionParser = $conditionParser;
    }

    /**
     * @inheritdoc
     * @return AbstractQueryNode
     */
    public function parse(TokenStream $tokenStream)
    {
        $operator = null;
        $queries = [];

        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $queries[] = $this->conditionParser->parse($tokenStream);

            if ($tokenStream->nextIf(Token::T_AMPERSAND)) {
                if ($operator === null) {
                    $operator = Token::T_AMPERSAND;
                } elseif ($operator !== Token::T_AMPERSAND) {
                    throw new SyntaxErrorException('Cannot mix "&" and "|" within a group');
                }
            } elseif ($tokenStream->nextIf(Token::T_VERTICAL_BAR)) {
                if ($operator === null) {
                    $operator = Token::T_VERTICAL_BAR;
                } elseif ($operator !== Token::T_VERTICAL_BAR) {
                    throw new SyntaxErrorException('Cannot mix "&" and "|" within a group');
                }
            } else {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        if ($operator === Token::T_VERTICAL_BAR) {
            return new OrNode($queries);
        } elseif ($operator === Token::T_AMPERSAND) {
            return new AndNode($queries);
        } else {
            return $queries[0];
        }
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPEN_PARENTHESIS);
    }
}
