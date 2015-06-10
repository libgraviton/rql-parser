<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GtNode;

/**
 */
class GtTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'gt';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GtNode($field, $value);
    }
}
