<?php
namespace Xiag\Rql\Parser\Node\Query;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 */
abstract class AbstractScalarOperatorNode extends AbstractQueryNode
{
    /**
     * @var string
     */
    protected $field;
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
