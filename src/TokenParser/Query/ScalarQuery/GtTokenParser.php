<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarQuery;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarQuery\GtNode;

/**
 */
class GtTokenParser extends AbstractScalarQueryTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'gt';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GtNode($field, $value);
    }
}
