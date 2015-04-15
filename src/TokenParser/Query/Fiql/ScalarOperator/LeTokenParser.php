<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Fiql\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Fiql\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\LeNode;

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
