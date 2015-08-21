<?php
namespace Xiag\Rql\Parser\Node\Query;

/**
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
     * @param mixed $values
     * @return void
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }
}
