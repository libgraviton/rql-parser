<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\Exception\SyntaxErrorException;

class Lexer
{
    /**
     * @var SubLexerInterface
     */
    protected $subLexer;

    /**
     * @param SubLexerInterface $subLexer
     */
    public function __construct(SubLexerInterface $subLexer = null)
    {
        $this->subLexer = $subLexer ?: static::createDefaultSubLexer();
    }

    /**
     * @param SubLexerInterface $subLexer
     * @return $this
     * @codeCoverageIgnore
     */
    public function setSubLexer(SubLexerInterface $subLexer)
    {
        $this->subLexer = $subLexer;
        return $this;
    }

    /**
     * @return SubLexerInterface
     * @codeCoverageIgnore
     */
    public function getSubLexer()
    {
        return $this->subLexer;
    }

    /**
     * @return SubLexerInterface
     */
    public static function createDefaultSubLexer()
    {
        return (new SubLexerChain())
            ->addSubLexer(new SubLexer\ConstantSubLexer())
            ->addSubLexer(new SubLexer\PunctuationSubLexer())
            ->addSubLexer(new SubLexer\FiqlOperatorSubLexer())
            ->addSubLexer(new SubLexer\RqlOperatorSubLexer())
            ->addSubLexer(new SubLexer\TypeSubLexer())

            ->addSubLexer(new SubLexer\GlobSubLexer())
            ->addSubLexer(new SubLexer\StringSubLexer())
            ->addSubLexer(new SubLexer\DatetimeSubLexer())
            ->addSubLexer(new SubLexer\NumberSubLexer())

            ->addSubLexer(new SubLexer\SortSubLexer());
    }

    /**
     * @param string $code
     * @return TokenStream
     * @throws SyntaxErrorException
     */
    public function tokenize($code)
    {
        $end    = strlen($code);
        $cursor = 0;
        $tokens = [];

        while ($cursor < $end) {
            $token = $this->subLexer->getTokenAt($code, $cursor);
            if ($token === null) {
                throw new SyntaxErrorException(
                    sprintf(
                        'Invalid character "%s" at position %d',
                        $code[$cursor],
                        $cursor
                    )
                );
            }

            $tokens[] = $token;
            $cursor = $token->getEnd();
        }
        $tokens[] = new Token(Token::T_END, '', $cursor, $cursor);

        return new TokenStream($tokens);
    }
}
