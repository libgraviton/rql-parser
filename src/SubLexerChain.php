<?php
namespace Graviton\RqlParser;

class SubLexerChain implements SubLexerInterface
{
    /**
     * @var SubLexerInterface[]
     */
    protected $subLexers = [];

    /**
     * @param SubLexerInterface $subLexer
     * @return $this
     */
    public function addSubLexer(SubLexerInterface $subLexer)
    {
        $this->subLexers[] = $subLexer;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        foreach ($this->subLexers as $subLexer) {
            $token = $subLexer->getTokenAt($code, $cursor);
            if ($token !== null) {
                return $token;
            }
        }

        return null;
    }
}
