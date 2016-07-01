<?php
namespace Xiag\Rql\Parser\ValueParser;

use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\SubParserInterface;

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
