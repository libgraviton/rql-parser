<?php
namespace Xiag\Rql\Parser\Node\Query\ScalarOperator;

use Xiag\Rql\Parser\Glob;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\RqlEncoder;

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
