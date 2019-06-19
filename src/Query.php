<?php
namespace Xiag\Rql\Parser;

use Xiag\Rql\Parser\Node\DeselectNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Node\LimitNode;

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
     * @return SelectNode
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
     * @return DeselectNode Deselect
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
     * @return AbstractQueryNode
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
     * @return SortNode
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
     * @return LimitNode
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
}
