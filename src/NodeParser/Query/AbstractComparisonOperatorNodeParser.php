<?php
namespace Graviton\RqlParser\NodeParser\Query;

use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\SubParserInterface;
use Graviton\RqlParser\Node\Query\AbstractComparisonOperatorNode;

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
