<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Fiql\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Fiql\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\GeNode;

/**
 */
class GeTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['ge', '>='];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GeNode($field, $value);
    }
}
