<?php
namespace Graviton\RqlParser;

abstract class AbstractNode
{
    /**
     * @return string
     */
    abstract public function getNodeName();

    /**
     * @return string
     */
    abstract public function toRql();
}
