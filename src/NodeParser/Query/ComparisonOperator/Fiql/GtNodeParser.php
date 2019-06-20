<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql;

use Graviton\RqlParser\Node\Query\ScalarOperator\GtNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonFiqlNodeParser;

class GtNodeParser extends AbstractComparisonFiqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'gt';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GtNode($field, $value);
    }
}
