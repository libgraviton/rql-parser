<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\ArrayOperator;

use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractArrayOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\InNode;

/**
 */
class InTokenParser extends AbstractArrayOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'in';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, array $values)
    {
        return new InNode($field, $values);
    }
}
