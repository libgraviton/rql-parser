<?php
namespace Graviton\RqlParser\Node\Query\ScalarOperator;

use Graviton\RqlParser\Node\Query\AbstractScalarOperatorNode;

/**
 * @codeCoverageIgnore
 */
class LtNode extends AbstractScalarOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'lt';
    }
}
