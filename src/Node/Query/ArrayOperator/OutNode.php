<?php
namespace Mrix\Rql\Parser\Node\Query\ArrayOperator;

use Mrix\Rql\Parser\Node\Query\AbstractArrayOperatorNode;

/**
 */
class OutNode extends AbstractArrayOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'out';
    }
}
