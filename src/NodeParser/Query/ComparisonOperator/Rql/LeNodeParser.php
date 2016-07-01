<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql;

use Xiag\Rql\Parser\Node\Query\ScalarOperator\LeNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

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
