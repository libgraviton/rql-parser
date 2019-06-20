<?php
namespace Graviton\RqlParser\Node\Query\LogicalOperator;

use Graviton\RqlParser\Node\Query\AbstractLogicalOperatorNode;

/**
 * @codeCoverageIgnore
 */
class AndNode extends AbstractLogicalOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'and';
    }
}
