<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql;

use Xiag\Rql\Parser\Node\Query\ScalarOperator\GtNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class GtNodeParser extends AbstractComparisonRqlNodeParser
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
