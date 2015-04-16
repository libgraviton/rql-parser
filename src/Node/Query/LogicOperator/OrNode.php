<?php
namespace Mrix\Rql\Parser\Node\Query\LogicOperator;

use Mrix\Rql\Parser\Node\Query\AbstractLogicOperatorNode;

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
