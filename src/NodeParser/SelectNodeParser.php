<?php
namespace Graviton\RqlParser\NodeParser;

use Graviton\RqlParser\Node\SelectNode;
use Graviton\RqlParser\NodeParser\Query\AbstractValueListNodeParser;

class SelectNodeParser extends AbstractValueListNodeParser
{
    public function getOperatorName()
    {
        return 'select';
    }

    public function getNode(array $elements)
    {
        return new SelectNode($elements);
    }
}
