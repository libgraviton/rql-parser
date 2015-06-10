<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LeNode;

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
