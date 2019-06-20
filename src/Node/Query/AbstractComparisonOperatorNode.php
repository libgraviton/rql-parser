<?php
namespace Graviton\RqlParser\Node\Query;

use Graviton\RqlParser\Node\AbstractQueryNode;

/**
 * @codeCoverageIgnore
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
