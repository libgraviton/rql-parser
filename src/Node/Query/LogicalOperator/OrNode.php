<?php
namespace Xiag\Rql\Parser\Node\Query\LogicalOperator;

use Xiag\Rql\Parser\Node\Query\AbstractLogicalOperatorNode;

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
