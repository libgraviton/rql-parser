<?php
namespace Graviton\RqlParser\Node\Query\ArrayOperator;

use Graviton\RqlParser\Node\Query\AbstractArrayOperatorNode;

/**
 * @codeCoverageIgnore
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
