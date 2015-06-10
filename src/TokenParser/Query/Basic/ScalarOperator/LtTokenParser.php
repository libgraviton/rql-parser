<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LtNode;

/**
 */
class LtTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'lt';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LtNode($field, $value);
    }
}
