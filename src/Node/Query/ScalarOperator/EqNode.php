<?php
namespace Graviton\RqlParser\Node\Query\ScalarOperator;

use Graviton\RqlParser\Node\Query\AbstractScalarOperatorNode;

/**
 * @codeCoverageIgnore
 */
class EqNode extends AbstractScalarOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'eq';
    }
}
