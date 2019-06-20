<?php
namespace Graviton\RqlParser\NodeParser;

use Graviton\RqlParser\Node\DeselectNode;
use Graviton\RqlParser\NodeParser\Query\AbstractValueListNodeParser;

class DeselectNodeParser extends AbstractValueListNodeParser
{
    public function getOperatorName()
    {
        return 'deselect';
    }

    public function getNode(array $elements)
    {
        return new DeselectNode($elements);
    }
}
