<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql;

use Xiag\Rql\Parser\Node\Query\ArrayOperator\OutNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

class OutNodeParser extends AbstractComparisonRqlNodeParser
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
