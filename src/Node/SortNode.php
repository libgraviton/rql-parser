<?php
namespace Xiag\Rql\Parser\Node;

use Xiag\Rql\Parser\AbstractNode;

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
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'sort';
    }

    /**
     * @param string $field
     * @param int $direction
     * @return void
     */
    public function addField($field, $direction = self::SORT_ASC)
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
