<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;

/**
 * Parser
 */
class Parser
{
    /**
     * @var TokenParserInterface[]
     */
    protected $tokenParsers = [];

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
     * @return TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return $this->tokenParsers;
    }

    /**
     * @return Parser
     */
    public static function createDefault()
    {
        return (new self())
            ->addTokenParser(new TokenParser\SelectTokenParser())
            ->addTokenParser(new TokenParser\QueryTokenParser(
                (new ExpressionParser())
                    ->registerTypeCaster('string', new TypeCaster\StringTypeCaster())
                    ->registerTypeCaster('integer', new TypeCaster\IntegerTypeCaster())
                    ->registerTypeCaster('float', new TypeCaster\FloatTypeCaster())
                    ->registerTypeCaster('boolean', new TypeCaster\BooleanTypeCaster())
            ))
            ->addTokenParser(new TokenParser\SortTokenParser())
            ->addTokenParser(new TokenParser\LimitTokenParser());
    }

    /**
     * @param TokenStream $tokenStream
     * @return Query
     * @throws SyntaxErrorException
     */
    public function parse(TokenStream $tokenStream)
    {
        $query = $this->createQuery();
        while (!$tokenStream->isEnd()) {
            $query->addNode($this->subparse($tokenStream));
            $tokenStream->nextIf(Token::T_AMPERSAND);
        }

        return $query;
    }

    /**
     * @param TokenStream $tokenStream
     * @return AbstractNode
     * @throws SyntaxErrorException
     */
    protected function subparse(TokenStream $tokenStream)
    {
        $token = $tokenStream->getCurrent();
        foreach ($this->tokenParsers as $tokenParser) {
            if ($tokenParser->supports($token)) {
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
     * @return Query
     */
    protected function createQuery()
    {
        return new Query();
    }
}
