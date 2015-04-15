<?php
namespace Mrix\Rql\Parser\TokenParser\Query;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\TokenParserInterface;
use Mrix\Rql\Parser\TokenParser\QueryTokenParser;
use Mrix\Rql\Parser\Exception\SyntaxErrorException;
use Mrix\Rql\Parser\Node\AbstractQueryNode;
use Mrix\Rql\Parser\Node\Query\LogicQuery\AndNode;
use Mrix\Rql\Parser\Node\Query\LogicQuery\OrNode;

/**
 */
class GroupTokenParser implements TokenParserInterface
{
    /**
     * @var QueryTokenParser
     */
    protected $queryTokenParser;

    /**
     * @param QueryTokenParser $queryTokenParser
     */
    public function __construct(QueryTokenParser $queryTokenParser)
    {
        $this->queryTokenParser = $queryTokenParser;
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
            $queries[] = $this->queryTokenParser->parse($tokenStream);

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
