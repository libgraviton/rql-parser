<?php
namespace Xiag\Rql\Parser\Node;

use Xiag\Rql\Parser\AbstractNode;

/**
 * @codeCoverageIgnore
 */
class DeselectNode extends AbstractNode
{
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
        return 'deselect';
    }

    /**
     * @param string $field
     * @param int|null $direction
     * @return void
     */
    public function addField($field, $direction = null)
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
