<?php
namespace Mrix\Rql\Parser\TokenParser\Query\FiqlOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractFiqlTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\GtNode;

/**
 */
class GtTokenParser extends AbstractFiqlTokenParser
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
