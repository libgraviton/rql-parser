<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Fiql\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Fiql\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\NeNode;

/**
 */
class NeTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['ne', '<>', '!='];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new NeNode($field, $value);
    }
}
