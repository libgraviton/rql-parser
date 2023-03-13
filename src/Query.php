<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\Node\DeselectNode;
use Graviton\RqlParser\Node\SelectNode;
use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\Node\SortNode;
use Graviton\RqlParser\Node\LimitNode;

/**
 * @codeCoverageIgnore
 */
class Query extends AbstractNode
{
    /**
     * @var SelectNode
     */
    protected $select;
    /**
     * @var DeselectNode
     */
    protected $deselect;
    /**
     * @var AbstractQueryNode
     */
    protected $query;
    /**
     * @var SortNode
     */
    protected $sort;
    /**
     * @var LimitNode
     */
    protected $limit;

    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'query';
    }

    /**
     * @return SelectNode|null
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param SelectNode $select
     * @return void
     */
    public function setSelect(SelectNode $select)
    {
        $this->select = $select;
    }

    /**
     * get Deselect
     *
     * @return DeselectNode|null Deselect
     */
    public function getDeselect()
    {
        return $this->deselect;
    }

    /**
     * set Deselect
     *
     * @param DeselectNode $deselect deselect
     *
     * @return void
     */
    public function setDeselect($deselect)
    {
        $this->deselect = $deselect;
    }

    /**
     * @return AbstractQueryNode|null
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param AbstractQueryNode $query
     * @return void
     */
    public function setQuery(AbstractQueryNode $query)
    {
        $this->query = $query;
    }

    /**
     * @return SortNode|null
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param SortNode $sort
     * @return void
     */
    public function setSort(SortNode $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return LimitNode|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param LimitNode $limit
     * @return void
     */
    public function setLimit(LimitNode $limit)
    {
        $this->limit = $limit;
    }

    /**
     * encodes to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        $nodes = [
            $this->query,
            $this->select,
            $this->deselect,
            $this->sort,
            $this->limit
        ];

        $usedParts = [];
        foreach ($nodes as $node) {
            if ($node instanceof AbstractNode) {
                $usedParts[] = $node->toRql();
            }
        }

        return implode('&', $usedParts);
    }
}
