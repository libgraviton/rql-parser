<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\LteNode;

/**
 */
class LteTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'lte';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LteNode($field, $value);
    }
}
