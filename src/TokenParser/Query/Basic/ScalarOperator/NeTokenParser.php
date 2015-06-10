<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;

/**
 */
class NeTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'ne';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new NeNode($field, $value);
    }
}
