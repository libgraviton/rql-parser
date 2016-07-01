<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql;

use Xiag\Rql\Parser\Node\Query\ScalarOperator\LeNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonFiqlNodeParser;

class LeNodeParser extends AbstractComparisonFiqlNodeParser
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
