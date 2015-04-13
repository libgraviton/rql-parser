<?php
namespace Mrix\Rql\Parser\TokenParser\Query\LogicQuery;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;
use Mrix\Rql\Parser\TokenParser\Query\AbstractLogicQueryTokenParser;
use Mrix\Rql\Parser\Node\Query\LogicQuery\OrNode;

/**
 */
class OrTokenParser extends AbstractLogicQueryTokenParser
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
