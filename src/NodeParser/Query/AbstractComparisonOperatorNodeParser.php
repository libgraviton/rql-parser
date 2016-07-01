<?php
namespace Xiag\Rql\Parser\NodeParser\Query;

use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\SubParserInterface;
use Xiag\Rql\Parser\Node\Query\AbstractComparisonOperatorNode;

abstract class AbstractComparisonOperatorNodeParser implements NodeParserInterface
{
    /**
     * @var SubParserInterface
     */
    protected $fieldNameParser;
    /**
     * @var SubParserInterface
     */
    protected $valueParser;

    /**
     * @param SubParserInterface $fieldNameParser
     * @param SubParserInterface $valueParser
     */
    public function __construct(SubParserInterface $fieldNameParser, SubParserInterface $valueParser)
    {
        $this->fieldNameParser = $fieldNameParser;
        $this->valueParser = $valueParser;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return AbstractComparisonOperatorNode
     */
    abstract protected function createNode($field, $value);

    /**
     * @return string
     */
    abstract protected function getOperatorName();
}
