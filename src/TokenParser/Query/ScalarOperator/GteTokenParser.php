<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\GteNode;

/**
 */
class GteTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'gte';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new GteNode($field, $value);
    }
}
