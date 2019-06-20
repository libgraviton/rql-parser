<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql;

use Graviton\RqlParser\Node\Query\ScalarOperator\GeNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class GeNodeParser extends AbstractComparisonRqlNodeParser
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
