<?php
namespace Graviton\RqlParser\Node;

use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\RqlEncoder;

/**
 * @codeCoverageIgnore
 */
class LimitNode extends AbstractNode
{
    /**
     * @var int
     */
    protected $limit;
    /**
     * @var int
     */
    protected $offset;

    /**
     * @param int $limit
     * @param int $offset
     */
    public function __construct($limit, $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'limit';
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return void
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * encodes to rql
     *
     * @return string rql
     */
    public function toRql()
    {
        $values = [$this->limit];
        if ($this->offset > 0) {
            $values[] = $this->offset;
        }

        return sprintf(
            '%s(%s)',
            $this->getNodeName(),
            RqlEncoder::encodeList($values, false)
        );
    }
}
