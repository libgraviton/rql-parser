<?php
namespace Mrix\Rql\Parser\TokenParser\Query\Basic\LogicOperator;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;
use Mrix\Rql\Parser\TokenParser\Query\Basic\AbstractLogicOperatorTokenParser;
use Mrix\Rql\Parser\Node\Query\LogicOperator\OrNode;

/**
 */
class OrTokenParser extends AbstractLogicOperatorTokenParser
{
    /**
     * @return string
     */
    protected function getOperatorName()
    {
        return 'or';
    }

    /**
     * @inheritdoc
     */
    protected function createNode(array $queries)
    {
        if (count($queries) < 2) {
            throw new SyntaxErrorException(
                sprintf(
                    '"%s" operator expects at least 2 parameters, %d given',
                    $this->getOperatorName(),
                    count($queries)
                )
            );
        }

        return new OrNode($queries);
    }
}
