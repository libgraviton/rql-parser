<?php
namespace Xiag\Rql\Parser\Node\Query;

/**
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
}
