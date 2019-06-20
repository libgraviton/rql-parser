<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql;

use Graviton\RqlParser\Node\Query\ArrayOperator\OutNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonFiqlNodeParser;

class OutNodeParser extends AbstractComparisonFiqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'out';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new OutNode($field, $value);
    }
}
