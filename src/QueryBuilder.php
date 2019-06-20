<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\Exception\UnknownNodeException;
use Graviton\RqlParser\Node\DeselectNode;
use Graviton\RqlParser\Node\SelectNode;
use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\Node\SortNode;
use Graviton\RqlParser\Node\LimitNode;
use Graviton\RqlParser\Node\Query\LogicalOperator\AndNode;

class QueryBuilder
{
    /**
     * @var Query
     */
    protected $query;

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
        } elseif ($node instanceof DeselectNode) {
            return $this->addDeselect($node);
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
     * @param DeselectNode $deselectNode
     * @return $this
     */
    public function addDeselect(DeselectNode $deselectNode)
    {
        $this->query->setDeselect($deselectNode);
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
