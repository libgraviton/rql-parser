<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarQuery;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarQuery\LtNode;

/**
 */
class LtTokenParser extends AbstractScalarQueryTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'lt';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LtNode($field, $value);
    }
}
