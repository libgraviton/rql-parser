<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;

/**
 * Expresssion parser interface
 */
interface ExpressionParserInterface
{
    /**
     * @param TokenStream $tokenStream
     * @return mixed
     * @throws SyntaxErrorException
     */
    public function parseScalar(TokenStream $tokenStream);

    /**
     * @param TokenStream $tokenStream
     * @return array
     * @throws SyntaxErrorException
     */
    public function parseArray(TokenStream $tokenStream);
}
