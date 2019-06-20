<?php
namespace Graviton\RqlParser\Node;

use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\RqlEncoder;

/**
 * @codeCoverageIgnore
 */
class SortNode extends AbstractNode
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    protected $rqlRepresenations = [
        self::SORT_ASC => '+',
        self::SORT_DESC => '-'
    ];

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

    /**
     * encodes to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        $sorts = array_map(function ($field, $direction) {
            return $this->rqlRepresenations[$direction].$field;
        }, array_keys($this->fields), $this->fields);

        return sprintf(
            '%s(%s)',
            $this->getNodeName(),
            implode(',', $sorts)
        );
    }
}
