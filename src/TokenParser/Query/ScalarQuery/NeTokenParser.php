<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarQuery;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarQuery\NeNode;

/**
 */
class NeTokenParser extends AbstractScalarQueryTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'ne';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new NeNode($field, $value);
    }
}
