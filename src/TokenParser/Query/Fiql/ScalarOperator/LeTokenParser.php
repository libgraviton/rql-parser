<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Fiql\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Fiql\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LeNode;

/**
 */
class LeTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['le', '<='];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LeNode($field, $value);
    }
}
