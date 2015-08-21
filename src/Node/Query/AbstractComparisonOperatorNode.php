<?php
namespace Xiag\Rql\Parser\Node\Query;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 */
abstract class AbstractComparisonOperatorNode extends AbstractQueryNode
{
    /**
     * @var string
     */
    protected $field;

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
}
