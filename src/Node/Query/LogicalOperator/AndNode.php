<?php
namespace Xiag\Rql\Parser\Node\Query\LogicalOperator;

use Xiag\Rql\Parser\Node\Query\AbstractLogicalOperatorNode;

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
