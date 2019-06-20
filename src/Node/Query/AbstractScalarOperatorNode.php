<?php
namespace Graviton\RqlParser\Node\Query;

use Graviton\RqlParser\RqlEncoder;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractScalarOperatorNode extends AbstractComparisonOperatorNode
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string $field
     * @param mixed $value
     */
    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * encodes to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        return sprintf(
            '%s(%s,%s)',
            $this->getNodeName(),
            RqlEncoder::encodeFieldName($this->getField()),
            RqlEncoder::encode($this->getValue())
        );
    }
}
