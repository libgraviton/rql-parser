<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql;

use Graviton\RqlParser\Node\Query\ScalarOperator\LtNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class LtNodeParser extends AbstractComparisonRqlNodeParser
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
