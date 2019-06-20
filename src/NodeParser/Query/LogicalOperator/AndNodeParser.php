<?php
namespace Graviton\RqlParser\NodeParser\Query\LogicalOperator;

use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\NodeParser\Query\AbstractLogicalOperatorNodeParser;
use Graviton\RqlParser\Node\Query\LogicalOperator\AndNode;

class AndNodeParser extends AbstractLogicalOperatorNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'and';
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

        return new AndNode($queries);
    }
}
