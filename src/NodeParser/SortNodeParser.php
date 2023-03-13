<?php
namespace Graviton\RqlParser\NodeParser;

use Graviton\RqlParser\Node\SortNode;
use Graviton\RqlParser\Token;

class SortNodeParser extends PrefixedListNodeParser
{

    function getNodeName() : string {
        return 'sort';
    }

    function addField(array $fields, Token $prefix, string $value) : array {
        $fields[$value] = $prefix->test(Token::T_PLUS) ? SortNode::SORT_ASC : SortNode::SORT_DESC;
        return $fields;
    }
}
