<?php
namespace Mrix\Rql\Parser\TokenParser;

use Mrix\Rql\Parser\TokenStream;
use Mrix\Rql\Parser\TokenParserInterface;
use Mrix\Rql\Parser\ExpressionParserInterface;
use Mrix\Rql\Parser\Exception\SyntaxErrorException;

/**
 */
class QueryTokenParser implements TokenParserInterface
{
    /**
     * @var ExpressionParserInterface
     */
    protected $expressionParser;
    /**
     * @var TokenParserInterface[]
     */
    protected $tokenParsers = [];

    /**
     * @param ExpressionParserInterface $expressionParser
     */
    public function __construct(ExpressionParserInterface $expressionParser)
    {
        $this->expressionParser = $expressionParser;
    }

    /**
     * @return ExpressionParserInterface
     */
    public function getExpressionParser()
    {
        return $this->expressionParser;
    }

    /**
     * @param TokenParserInterface $tokenParser
     * @return $this
     */
    public function addTokenParser(TokenParserInterface $tokenParser)
    {
        $this->tokenParsers[] = $tokenParser;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $token = $tokenStream->getCurrent();
        foreach ($this->tokenParsers as $tokenParser) {
            if ($tokenParser->supports($tokenStream)) {
                return $tokenParser->parse($tokenStream);
            }
        }

        throw new SyntaxErrorException(
            sprintf(
                'Unexpected token "%s" (%s) at position %d',
                $token->getValue(),
                $token->getName(),
                $token->getPosition()
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenStream $tokenStream)
    {
        foreach ($this->tokenParsers as $tokenParser) {
            if ($tokenParser->supports($tokenStream)) {
                return true;
            }
        }

        return false;
    }
}
