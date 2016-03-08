<?php

namespace Xiag\Rql\Parser\Node;

use Xiag\Rql\Parser\AbstractNode;

/**
 * Class SearchNode
 * @package Xiag\Rql\Parser\Node
 */
class SearchNode extends AbstractNode
{
    /**
     * @var array
     */
    protected $searchTerms;

    /**
     * @param array $searchTerms
     */
    public function __construct(array $searchTerms = [])
    {
        $this->searchTerms = $searchTerms;
    }

    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'search';
    }

    /**
     * @param string $searchTerm
     * @return void
     */
    public function addSearchTerm($searchTerm)
    {
        $this->searchTerms[$searchTerm] = $searchTerm;
    }

    /**
     * @return array
     */
    public function getSearchTerms()
    {
        return $this->searchTerms;
    }


}