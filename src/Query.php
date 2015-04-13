<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\UnknownNodeException;
use Mrix\Rql\Parser\Node\SelectNode;
use Mrix\Rql\Parser\Node\AbstractQueryNode;
use Mrix\Rql\Parser\Node\SortNode;
use Mrix\Rql\Parser\Node\LimitNode;
use Mrix\Rql\Parser\Node\Query\LogicQuery\AndNode;

/**
 */
class Query
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
     * @param AbstractNode $node
     * @return $this
     */
    public function addNode(AbstractNode $node)
    {
        if ($node instanceof SelectNode) {
            return $this->addSelect($node);
        } elseif ($node instanceof AbstractQueryNode) {
            return $this->addQuery($node);
        } elseif ($node instanceof SortNode) {
            return $this->addSort($node);
        } elseif ($node instanceof LimitNode) {
            return $this->addLimit($node);
        }

        throw new UnknownNodeException(sprintf('Unknown node type "%s"', get_class($node)));
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
     * @return $this
     */
    public function addSelect(SelectNode $select)
    {
        $this->select = $select;

        return $this;
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
     * @return $this
     */
    public function addQuery(AbstractQueryNode $query)
    {
        if ($this->query === null) {
            $this->query = $query;
        } else {
            $this->query = new AndNode([$this->query, $query]);
        }

        return $this;
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
     * @return $this
     */
    public function addSort(SortNode $sort)
    {
        $this->sort = $sort;

        return $this;
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
     * @return $this
     */
    public function addLimit(LimitNode $limit)
    {
        $this->limit = $limit;

        return $this;
    }
}
