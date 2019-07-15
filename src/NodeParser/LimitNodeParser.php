<?php
namespace Graviton\RqlParser\NodeParser;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\Node\LimitNode;
use Graviton\RqlParser\SubParserInterface;

class LimitNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $valueParser;

    /**
     * @param SubParserInterface $valueParser
     */
    public function __construct(SubParserInterface $valueParser)
    {
        $this->valueParser = $valueParser;
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $limit = null;
        $offset = 0;

        $tokenStream->expect(Token::T_OPERATOR, 'limit');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $limit = $this->valueParser->parse($tokenStream);
        if ($tokenStream->nextIf(Token::T_COMMA)) {
            $offset = $this->valueParser->parse($tokenStream);
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
