<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Basic\ScalarOperator;

use Mrix\Rql\Parser\TokenParser\Query\Basic\AbstractScalarOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarOperator\LikeNode;

/**
 */
class LikeTokenParser extends AbstractScalarOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'like';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new LikeNode($field, $value);
    }
}
