<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ScalarQuery;

use Mrix\Rql\Parser\TokenParser\Query\AbstractScalarQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\ScalarQuery\LteNode;

/**
 */
class LteTokenParser extends AbstractScalarQueryTokenParser
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
