<?php
namespace Graviton\RqlParser\NodeParser;

use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\Node\SortNode;
use Graviton\RqlParser\SubParserInterface;

abstract class PrefixedListNodeParser implements NodeParserInterface
{

    protected SubParserInterface $fieldNameParser;

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
    public function parse(TokenStream $tokenStream) : AbstractNode
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, $this->getNodeName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $prefix = $tokenStream->expect($this->getAllowedPrefixes());
            $value = $this->fieldNameParser->parse($tokenStream);

            $fields = $this->addField($fields, $prefix, $value);

            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->getNode($fields);
    }

    public function getAllowedPrefixes() : array {
        return [Token::T_PLUS, Token::T_MINUS];
    }

    abstract function getNodeName() : string;

    abstract function getNode(array $fields) : AbstractNode;

    abstract function addField(array $fields, Token $prefix, string $value) : array;

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, $this->getNodeName());
    }
}
