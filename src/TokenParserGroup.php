<?php
namespace Xiag\Rql\Parser;

use Xiag\Rql\Parser\Exception\SyntaxErrorException;

/**
 */
class TokenParserGroup extends AbstractTokenParser
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
        if ($this->getParser() !== null) {
            $tokenParser->setParser($this->getParser());
        }
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
     * @inheritdoc
     */
    public function setParser(Parser $parser)
    {
        foreach ($this->tokenParsers as $tokenParser) {
            $tokenParser->setParser($parser);
        }

        parent::setParser($parser);
    }

    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        foreach ($this->tokenParsers as $tokenParser) {
            if ($tokenParser->supports($tokenStream)) {
                return $tokenParser->parse($tokenStream);
            }
        }

        throw new SyntaxErrorException(
            sprintf(
                'Unexpected token "%s" (%s) at position %d',
                $tokenStream->getCurrent()->getValue(),
                $tokenStream->getCurrent()->getName(),
                $tokenStream->getCurrent()->getPosition()
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
