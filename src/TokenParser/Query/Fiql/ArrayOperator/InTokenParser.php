<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Fiql\ArrayOperator;

use Mrix\Rql\Parser\TokenParser\Query\Fiql\AbstractArrayOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ArrayOperator\InNode;

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
