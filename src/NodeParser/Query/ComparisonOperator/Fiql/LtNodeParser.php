<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql;

use Xiag\Rql\Parser\Node\Query\ScalarOperator\LtNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonFiqlNodeParser;

class LtNodeParser extends AbstractComparisonFiqlNodeParser
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
