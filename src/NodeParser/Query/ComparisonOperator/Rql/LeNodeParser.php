<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql;

use Graviton\RqlParser\Node\Query\ScalarOperator\LeNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class LeNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'le';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LeNode($field, $value);
    }
}
