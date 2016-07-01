<?php
namespace Xiag\Rql\Parser\Node\Query\ScalarOperator;

use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;

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
