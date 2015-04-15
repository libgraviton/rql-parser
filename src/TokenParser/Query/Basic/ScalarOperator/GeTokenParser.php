<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\GeNode;

/**
 */
class GeTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'ge';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GeNode($field, $value);
    }
}
