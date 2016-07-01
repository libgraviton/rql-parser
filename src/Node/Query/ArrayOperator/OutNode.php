<?php
namespace Xiag\Rql\Parser\Node\Query\ArrayOperator;

use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;

/**
 * @codeCoverageIgnore
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
