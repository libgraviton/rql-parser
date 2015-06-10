<?php
namespace Xiag\Rql\Parser\TokenParser\Query\Basic\LogicOperator;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\TokenParser\Query\Basic\AbstractLogicOperatorTokenParser;
use Xiag\Rql\Parser\Node\Query\LogicOperator\NotNode;

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
