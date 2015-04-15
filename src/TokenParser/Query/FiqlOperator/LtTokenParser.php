<?php
namespace Mrix\Rql\Parser\TokenParser\Query\FiqlOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractFiqlTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\LtNode;

/**
 */
class LtTokenParser extends AbstractFiqlTokenParser
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
