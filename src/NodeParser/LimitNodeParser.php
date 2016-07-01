<?php
namespace Xiag\Rql\Parser\NodeParser;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\SubParserInterface;

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
        $offset = null;

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
