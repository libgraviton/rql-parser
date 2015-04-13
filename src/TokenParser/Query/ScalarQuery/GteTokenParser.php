<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarQuery;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarQuery\GteNode;

/**
 */
class GteTokenParser extends AbstractScalarQueryTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'gte';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GteNode($field, $value);
    }
}
