<?php
namespace Mrix\Rql\Parser\TokenParser\Query\FiqlOperator;

use Mrix\Rql\Parser\Node\Query\ScalarQuery\GteNode;

/**
 */
class GteTokenParser extends AbstractFiqlTokenParser
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
