<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql;

use Graviton\RqlParser\Node\Query\ScalarOperator\EqNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonFiqlNodeParser;

class EqNodeParser extends AbstractComparisonFiqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'eq';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new EqNode($field, $value);
    }
}
