<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Fiql\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Fiql\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LtNode;

/**
 */
class LtTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['lt', '<'];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LtNode($field, $value);
    }
}
