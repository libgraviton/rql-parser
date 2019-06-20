<?php
namespace Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql;

use Graviton\RqlParser\Node\Query\ArrayOperator\InNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class InNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'in';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new InNode($field, $value);
    }
}
