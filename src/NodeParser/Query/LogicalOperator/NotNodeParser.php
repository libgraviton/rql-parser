<?php
namespace Graviton\RqlParser\NodeParser\Query\LogicalOperator;

use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\NodeParser\Query\AbstractLogicalOperatorNodeParser;
use Graviton\RqlParser\Node\Query\LogicalOperator\NotNode;

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
