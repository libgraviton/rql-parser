<?php
namespace Mrix\Rql\Parser\Node;

use Mrix\Rql\Parser\AbstractNode;

/**
 */
class SortNode extends AbstractNode
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * @param string $field
     * @param int|null $direction
     * @return void
     */
    public function addField($field, $direction)
    {
        $this->fields[$field] = $direction;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
