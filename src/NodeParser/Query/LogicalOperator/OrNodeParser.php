<?php
namespace Xiag\Rql\Parser\NodeParser\Query\LogicalOperator;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\NodeParser\Query\AbstractLogicalOperatorNodeParser;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\OrNode;

class OrNodeParser extends AbstractLogicalOperatorNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'or';
    }

    /**
     * @inheritdoc
     */
    protected function createNode(array $queries)
    {
        if (count($queries) < 2) {
            throw new SyntaxErrorException(
                sprintf(
                    '"%s" operator expects at least 2 parameters, %d given',
                    $this->getOperatorName(),
                    count($queries)
                )
            );
        }

        return new OrNode($queries);
    }
}
