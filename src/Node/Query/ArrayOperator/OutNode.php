<?php
namespace Graviton\RqlParser\Node\Query\ArrayOperator;

use Graviton\RqlParser\Node\Query\AbstractArrayOperatorNode;

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
