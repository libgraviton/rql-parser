<?php
namespace Graviton\RqlParser\Node;

use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\RqlEncoder;

/**
 * @codeCoverageIgnore
 */
class DeselectNode extends AbstractNode
{
    /**
     * @var array
     */
    protected $fields = [];

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

    /**
     * encodes to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        return sprintf(
            '%s(%s)',
            $this->getNodeName(),
            RqlEncoder::encodeList($this->fields)
        );
    }
}
