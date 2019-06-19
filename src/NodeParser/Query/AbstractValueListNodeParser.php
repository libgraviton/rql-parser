<?php
namespace Xiag\Rql\Parser\NodeParser\Query;

use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;

abstract class AbstractValueListNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $fieldNameParser;

    protected $separator = Token::T_COMMA;

    /**
     * @param SubParserInterface $fieldNameParser
     */
    public function __construct(SubParserInterface $fieldNameParser)
    {
        $this->fieldNameParser = $fieldNameParser;
    }

    abstract public function getOperatorName();

    abstract public function getNode(array $elements);

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $fields[] = $this->fieldNameParser->parse($tokenStream);
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $this->getNode($fields);
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, $this->getOperatorName());
    }
}
