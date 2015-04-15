<?php
namespace Mrix\Rql\Parser\TokenParser;

use Mrix\Rql\Parser\Token;
use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\AbstractTokenParser;
use Mrix\Rql\Parser\Node\SortNode;

/**
 */
class SortTokenParser extends AbstractTokenParser
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'sort');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $fields[] = $tokenStream->expect(Token::T_STRING)->getValue();
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SortNode($this->processFields($fields));
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'sort');
    }

    /**
     * @param array $fields
     * @return array
     */
    protected function processFields(array $fields)
    {
        $result = [];
        foreach ($fields as $field) {
            if ($field[0] === '+') {
                $result[substr($field, 1)] = SortNode::SORT_ASC;
            } elseif ($field[0] === '-') {
                $result[substr($field, 1)] = SortNode::SORT_DESC;
            } else {
                $result[$field] = SortNode::SORT_ASC;
            }
        }

        return $result;
    }
}
