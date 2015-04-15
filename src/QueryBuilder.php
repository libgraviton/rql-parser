<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\UnknownNodeException;
use Mrix\Rql\Parser\Node\SelectNode;
use Mrix\Rql\Parser\Node\AbstractQueryNode;
use Mrix\Rql\Parser\Node\SortNode;
use Mrix\Rql\Parser\Node\LimitNode;
use Mrix\Rql\Parser\Node\Query\LogicOperator\AndNode;

/**
 */
class QueryBuilder
{
    /**
     * @var Query
     */
    protected $query;

    /**
     *
     */
    public function __construct()
    {
        $this->query = new Query();
    }

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
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param SelectNode $select
     * @return $this
     */
    public function addSelect(SelectNode $select)
    {
        $this->query->setSelect($select);

        return $this;
    }

    /**
     * @param AbstractQueryNode $query
     * @return $this
     */
    public function addQuery(AbstractQueryNode $query)
    {
        if ($this->query->getQuery() === null) {
            $this->query->setQuery($query);
        } else {
            $this->query->setQuery(new AndNode([$this->query, $query]));
        }

        return $this;
    }

    /**
     * @param SortNode $sort
     * @return $this
     */
    public function addSort(SortNode $sort)
    {
        $this->query->setSort($sort);

        return $this;
    }

    /**
     * @param LimitNode $limit
     * @return $this
     */
    public function addLimit(LimitNode $limit)
    {
        $this->query->setLimit($limit);

        return $this;
    }
}
