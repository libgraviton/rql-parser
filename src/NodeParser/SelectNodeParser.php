<?php
namespace Xiag\Rql\Parser\NodeParser;

use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\NodeParser\Query\AbstractValueListNodeParser;

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
