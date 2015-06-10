<?php
namespace Xiag\Rql\Parser\Node\Query\LogicOperator;

use Xiag\Rql\Parser\Node\Query\AbstractLogicOperatorNode;

/**
 */
class OrNode extends AbstractLogicOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'or';
    }
}
