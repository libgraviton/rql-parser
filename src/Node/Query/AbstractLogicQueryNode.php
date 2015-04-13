<?php
namespace Mrix\Rql\Parser\Node\Query;

use Mrix\Rql\Parser\Node\AbstractQueryNode;

/**
 */
abstract class AbstractLogicQueryNode extends AbstractQueryNode
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
}
