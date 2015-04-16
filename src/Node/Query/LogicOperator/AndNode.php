<?php
namespace Mrix\Rql\Parser\Node\Query\LogicOperator;

use Mrix\Rql\Parser\Node\Query\AbstractLogicOperatorNode;

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
