<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\LtNode;

/**
 */
class LtTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'lt';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LtNode($field, $value);
    }
}
