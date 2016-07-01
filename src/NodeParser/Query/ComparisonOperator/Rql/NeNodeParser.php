<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql;

use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class NeNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'ne';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new NeNode($field, $value);
    }
}
