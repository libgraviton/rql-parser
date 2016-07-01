<?php
namespace Xiag\Rql\Parser\NodeParser\Query\LogicalOperator;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\NodeParser\Query\AbstractLogicalOperatorNodeParser;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\NotNode;

class NotNodeParser extends AbstractLogicalOperatorNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'not';
    }

    /**
     * @inheritdoc
     */
    protected function createNode(array $queries)
    {
        if (count($queries) !== 1) {
            throw new SyntaxErrorException(
                sprintf(
                    '"%s" operator expects 1 parameter, %d given',
                    $this->getOperatorName(),
                    count($queries)
                )
            );
        }

        return new NotNode($queries);
    }
}
