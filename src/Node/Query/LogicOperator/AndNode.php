<?php
namespace Xiag\Rql\Parser\Node\Query\LogicOperator;

use Xiag\Rql\Parser\Node\Query\AbstractLogicOperatorNode;

/**
 */
class AndNode extends AbstractLogicOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'and';
    }
}
