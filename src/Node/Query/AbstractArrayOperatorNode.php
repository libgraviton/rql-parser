<?php
namespace Mrix\Rql\Parser\Node\Query;

use Mrix\Rql\Parser\Node\AbstractQueryNode;

/**
 */
abstract class AbstractArrayOperatorNode extends AbstractQueryNode
{
    /**
     * @var string
     */
    protected $field;
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
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return void
     */
    public function setField($field)
    {
        $this->field = $field;
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
