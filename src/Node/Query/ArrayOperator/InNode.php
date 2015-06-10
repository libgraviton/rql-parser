<?php
namespace Xiag\Rql\Parser\Node\Query\ArrayOperator;

use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;

/**
 */
class InNode extends AbstractArrayOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'in';
    }
}
