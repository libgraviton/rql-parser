<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Fiql\ArrayOperator;

use Xiag\Rql\Parser\TokenParser\Query\Fiql\AbstractArrayOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\OutNode;

/**
 */
class OutTokenParser extends AbstractArrayOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['out'];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, array $values)
    {
        return new OutNode($field, $values);
    }
}
