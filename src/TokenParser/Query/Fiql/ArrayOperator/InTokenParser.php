<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Fiql\ArrayOperator;

use Xiag\Rql\Parser\TokenParser\Query\Fiql\AbstractArrayOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\InNode;

/**
 */
class InTokenParser extends AbstractArrayOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['in'];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, array $values)
    {
        return new InNode($field, $values);
    }
}
