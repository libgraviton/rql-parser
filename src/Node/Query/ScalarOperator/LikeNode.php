<?php
namespace Graviton\RqlParser\Node\Query\ScalarOperator;

use Graviton\RqlParser\Glob;
use Graviton\RqlParser\Node\Query\AbstractScalarOperatorNode;
use Graviton\RqlParser\RqlEncoder;

/**
 * @codeCoverageIgnore
 */
class LikeNode extends AbstractScalarOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'like';
    }

    /**
     * encodes to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        if ($this->getValue() instanceof Glob) {
            $value = $this->getValue()->toRql();
        } else {
            $value = RqlEncoder::encode($this->getValue());
        }

        return sprintf(
            '%s(%s,%s)',
            $this->getNodeName(),
            RqlEncoder::encodeFieldName($this->getField()),
            $value
        );
    }
}
