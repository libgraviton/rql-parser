<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Fiql\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Fiql\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GtNode;

/**
 */
class GtTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['gt', '>'];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GtNode($field, $value);
    }
}
