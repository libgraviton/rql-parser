<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql;

use Graviton\RqlParser\Node\Query\ScalarOperator\GeNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonFiqlNodeParser;

class GeNodeParser extends AbstractComparisonFiqlNodeParser
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
