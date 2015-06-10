<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GeNode;

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
