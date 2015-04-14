<?php
namespace Mrix\Rql\Parser\TokenParser\Query\ArrayQuery;

use Mrix\Rql\Parser\TokenParser\Query\AbstractArrayOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\ArrayQuery\OutNode;

/**
 */
class OutTokenParser extends AbstractArrayOperatorTokenParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'out';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, array $values)
    {
        return new OutNode($field, $values);
    }
}
