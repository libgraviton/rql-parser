<?php
namespace Mrix\Rql\Parser\TokenParser\Query\LogicOperator;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;
use Mrix\Rql\Parser\TokenParser\Query\AbstractLogicOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\LogicOperator\NotNode;

/**
 */
class NotTokenParser extends AbstractLogicOperatorTokenParser
{
    /**
     * @return string
     */
    protected function getOperatorName()
    {
        return 'not';
    }

    /**
     * @inheritdoc
     */
    protected function createNode(array $queries)
    {
        if (count($queries) !== 1) {
            throw new SyntaxErrorException(
                sprintf(
                    '"%s" operator expects 1 parameter, %d given',
                    $this->getOperatorName(),
                    count($queries)
                )
            );
        }

        return new NotNode($queries);
    }
}
