<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Basic\ArrayOperator;

use Mrix\Rql\Parser\TokenParser\Query\Basic\AbstractArrayOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ArrayOperator\OutNode;

/**
 */
class OutTokenParser extends AbstractArrayOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'out';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, array $values)
    {
        return new OutNode($field, $values);
    }
}
