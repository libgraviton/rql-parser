<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\ArrayOperator;

use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractArrayOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\OutNode;

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
