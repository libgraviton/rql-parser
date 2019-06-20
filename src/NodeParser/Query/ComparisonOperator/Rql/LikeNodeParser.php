<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql;

use Graviton\RqlParser\Node\Query\ScalarOperator\LikeNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class LikeNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'like';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LikeNode($field, $value);
    }
}
