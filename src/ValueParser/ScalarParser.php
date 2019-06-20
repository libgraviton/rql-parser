<?php
namespace Graviton\RqlParser\ValueParser;

use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\TypeCasterInterface;
use Graviton\RqlParser\SubParserInterface;
use Graviton\RqlParser\Exception\SyntaxErrorException;

class ScalarParser implements SubParserInterface
{

    /**
     * @var string
     */
    public const DATETIME_FORMAT = 'Y-m-d\TH:i:sO';

    /**
     * @var TypeCasterInterface[]
     */
    protected $typeCasters = [];

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        if (($typeToken = $tokenStream->nextIf(Token::T_TYPE)) !== null) {
            $tokenStream->expect(Token::T_COLON);
            $value = $this->getTypeCaster($typeToken->getValue())->typeCast($tokenStream->next());
        } else {
            $value = $this->getScalarValue($tokenStream->next());
        }

        return $value;
    }

    /**
     * @param string $type
     * @param TypeCasterInterface $typeCaster
     * @return $this
     */
    public function registerTypeCaster($type, TypeCasterInterface $typeCaster)
    {
        $this->typeCasters[$type] = $typeCaster;
        return $this;
    }

    /**
     * @param string $type
     * @return TypeCasterInterface
     */
    public function getTypeCaster($type)
    {
        if (!isset($this->typeCasters[$type])) {
            throw new SyntaxErrorException(sprintf('Unknown type "%s"', $type));
        }

        return $this->typeCasters[$type];
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
        } elseif ($token->test(Token::T_DATE)) {
            return \DateTime::createFromFormat(self::DATETIME_FORMAT, $token->getValue());
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
}
