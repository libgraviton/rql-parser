<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Fiql\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Fiql\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\GtNode;

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
