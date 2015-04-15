<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\EqNode;

/**
 */
class EqTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'eq';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new EqNode($field, $value);
    }
}
