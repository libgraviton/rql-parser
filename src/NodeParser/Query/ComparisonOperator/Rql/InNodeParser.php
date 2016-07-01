<?php
namespace Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql;

use Xiag\Rql\Parser\Node\Query\ArrayOperator\InNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

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
