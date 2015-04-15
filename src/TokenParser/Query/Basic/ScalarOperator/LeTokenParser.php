<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\LeNode;

/**
 */
class LeTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'le';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LeNode($field, $value);
    }
}
