<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarQuery;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarQuery\EqNode;

/**
 */
class EqTokenParser extends AbstractScalarQueryTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'eq';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new EqNode($field, $value);
    }
}
