<?php
namespace Xiag\Rql\Parser;

use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Node\LimitNode;

/**
 */
class Query extends AbstractNode
{
    /**
     * @var SelectNode
     */
    protected $select;
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
