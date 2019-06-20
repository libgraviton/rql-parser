<?php
namespace Graviton\RqlParser\ValueParser;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\SubParserInterface;

class ArrayParser implements SubParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $itemParser;

    /**
     * @param SubParserInterface $itemParser
     */
    public function __construct(SubParserInterface $itemParser)
    {
        $this->itemParser = $itemParser;
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $values = [];
        do {
            $values[] = $this->itemParser->parse($tokenStream);
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $values;
    }
}
