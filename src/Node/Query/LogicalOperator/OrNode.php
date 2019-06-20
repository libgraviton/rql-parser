<?php
namespace Graviton\RqlParser\Node\Query\LogicalOperator;

use Graviton\RqlParser\Node\Query\AbstractLogicalOperatorNode;

/**
 * @codeCoverageIgnore
 */
class OrNode extends AbstractLogicalOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'or';
    }
}
