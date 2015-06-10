<?php
namespace Xiag\Rql\Parser;

use Xiag\Rql\Parser\Exception\UnknownNodeException;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;

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

        throw new UnknownNodeException(sprintf('Unknown node type "%s" (%s)', $node->getNodeName(), get_class($node)));
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
        $current = $this->query->getQuery();
        if ($current === null) {
            $this->query->setQuery($query);
        } elseif ($current instanceof AndNode) {
            $current->addQuery($query);
        } else {
            $this->query->setQuery(new AndNode([$current, $query]));
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
