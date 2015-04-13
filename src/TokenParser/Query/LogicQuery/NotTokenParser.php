<?php
namespace Mrix\Rql\Parser\TokenParser\Query\LogicQuery;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;
use Mrix\Rql\Parser\TokenParser\Query\AbstractLogicQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\LogicQuery\NotNode;

/**
 */
class NotTokenParser extends AbstractLogicQueryTokenParser
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
