<?php
namespace Xiag\Rql\Parser\NodeParser\Query;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\OrNode;

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
