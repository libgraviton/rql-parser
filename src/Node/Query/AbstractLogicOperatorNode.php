<?php
namespace Xiag\Rql\Parser\Node\Query;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 */
abstract class AbstractLogicOperatorNode extends AbstractQueryNode
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
