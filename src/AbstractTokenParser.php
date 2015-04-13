<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;

/**
 */
abstract class AbstractTokenParser implements TokenParserInterface
{
    /**
     * @param TokenStream $tokenStream
     * @return mixed
     * @throws SyntaxErrorException
     */
    protected function parseScalar(TokenStream $tokenStream)
    {
        if (($typeToken = $tokenStream->nextIf(Token::T_TYPE)) !== null) {
            $value = $this->typeCastValue($this->getScalarValue($tokenStream->next()), $typeToken->getValue());
        } else {
            $value = $this->getScalarValue($tokenStream->next());
        }

        return $value;
    }

    /**
     * @param TokenStream $tokenStream
     * @return array
     * @throws SyntaxErrorException
     */
    protected function parseArray(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $values = [];
        do {
            $values[] = $this->parseScalar($tokenStream);
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return $values;
    }

    /**
     * @param Token $token
     * @return mixed
     * @throws SyntaxErrorException
     */
    protected function getScalarValue(Token $token)
    {
        if ($token->test(Token::T_FALSE)) {
            return false;
        } elseif ($token->test(Token::T_TRUE)) {
            return true;
        } elseif ($token->test(Token::T_NULL)) {
            return null;
        } elseif ($token->test(Token::T_EMPTY)) {
            return '';
        } elseif ($token->test(Token::T_STRING)) {
            return $token->getValue();
        } elseif ($token->test(Token::T_INTEGER)) {
            return (int)$token->getValue();
        } elseif ($token->test(Token::T_FLOAT)) {
            return (float)$token->getValue();
        }

        throw new SyntaxErrorException(
            sprintf(
                'Invalid scalar token "%s" (%s)',
                $token->getValue(),
                $token->getName()
            )
        );
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function typeCastValue($value, $type)
    {
        //TODO: implement type casting
        return $value;
    }
}
