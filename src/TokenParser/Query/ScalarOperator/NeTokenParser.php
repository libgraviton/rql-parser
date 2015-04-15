<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\NeNode;

/**
 */
class NeTokenParser extends AbstractScalarOperatorTokenParser
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
