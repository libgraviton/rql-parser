<?php
namespace Mrix\Rql\Parser\TokenParser\Query\FiqlOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractFiqlTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\NeNode;

/**
 */
class NeTokenParser extends AbstractFiqlTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorNames()
    {
        return ['ne', '<>', '!='];
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new NeNode($field, $value);
    }
}
