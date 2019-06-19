<?php
namespace Xiag\Rql\Parser\NodeParser;

use Xiag\Rql\Parser\Node\DeselectNode;
use Xiag\Rql\Parser\NodeParser\Query\AbstractValueListNodeParser;

class DeselectNodeParser extends AbstractValueListNodeParser
{
    public function getOperatorName() {
        return 'deselect';
    }

    public function getNode(array $elements)
    {
        return new DeselectNode($elements);
    }
}
