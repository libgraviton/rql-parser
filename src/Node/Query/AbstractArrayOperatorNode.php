<?php
namespace Graviton\RqlParser\Node\Query;

use Graviton\RqlParser\RqlEncoder;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractArrayOperatorNode extends AbstractComparisonOperatorNode
{
    /**
     * @var array
     */
    protected $values;

    /**
     * @param string $field
     * @param array $values
     */
    public function __construct($field, array $values)
    {
        $this->field = $field;
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @return void
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * encodes to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        return sprintf(
            '%s(%s,(%s))',
            $this->getNodeName(),
            RqlEncoder::encodeFieldName($this->getField()),
            RqlEncoder::encodeList($this->getValues())
        );
    }
}
