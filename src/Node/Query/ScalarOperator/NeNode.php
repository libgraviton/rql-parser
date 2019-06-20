<?php
namespace Graviton\RqlParser\Node\Query\ScalarOperator;

use Graviton\RqlParser\Node\Query\AbstractScalarOperatorNode;

/**
 * @codeCoverageIgnore
 */
class NeNode extends AbstractScalarOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'ne';
    }
}
