<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\LogicOperator;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractLogicOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\LogicOperator\OrNode;

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
