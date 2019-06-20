<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql;

use Graviton\RqlParser\Node\Query\ScalarOperator\LtNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonFiqlNodeParser;

class LtNodeParser extends AbstractComparisonFiqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'lt';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LtNode($field, $value);
    }
}
