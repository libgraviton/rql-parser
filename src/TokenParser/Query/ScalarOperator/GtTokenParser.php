<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\GtNode;

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
