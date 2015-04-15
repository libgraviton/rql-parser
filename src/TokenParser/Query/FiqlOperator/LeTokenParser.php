<?php
namespace Mrix\Rql\Parser\TokenParser\Query\FiqlOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractFiqlTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\LeNode;

/**
 */
class LeTokenParser extends AbstractFiqlTokenParser
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
