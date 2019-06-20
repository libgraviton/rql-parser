<?php
namespace Graviton\RqlParser\Node\Query;

use Graviton\RqlParser\Node\AbstractQueryNode;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractLogicalOperatorNode extends AbstractQueryNode
{
    /**
     * @var AbstractQueryNode[]
     */
    protected $queries;

    /**
     * @param AbstractQueryNode[] $queries
     */
    public function __construct(array $queries = [])
    {
        $this->queries = $queries;
    }

    /**
     * @param AbstractQueryNode $query
     * @return void
     */
    public function addQuery(AbstractQueryNode $query)
    {
        $this->queries[] = $query;
    }

    /**
     * @return AbstractQueryNode[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param AbstractQueryNode[] $queries
     * @return void
     */
    public function setQueries(array $queries)
    {
        $this->queries = $queries;
    }

    /**
     * convert to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        $queryNodes = array_map(
            function ($item) {
                return $item->toRql();
            },
            $this->queries
        );

        return sprintf(
            '%s(%s)',
            $this->getNodeName(),
            implode(',', $queryNodes)
        );
    }
}
