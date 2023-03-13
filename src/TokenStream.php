<?php
namespace Graviton\RqlParser;

use Graviton\RqlParser\Exception\SyntaxErrorException;

class TokenStream implements \Countable
{
    /**
     * @var Token[]
     */
    protected $tokens;
    /**
     * @var int Current position
     */
    protected $current;

    /**
     * @param array $tokens An array of tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens  = $tokens;
        $this->current = 0;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->tokens);
    }

    /**
     * @return Token
     * @throws SyntaxErrorException If the stream ends
     */
    public function next()
    {
        if (!isset($this->tokens[++$this->current])) {
            throw new SyntaxErrorException('Unexpected end of stream');
        }

        return $this->tokens[$this->current - 1];
    }

    /**
     * @param int|int[] $type
     * @param string|string[] $value
     * @return Token|null
     * @throws SyntaxErrorException If the stream ends
     */
    public function nextIf($type, $value = null)
    {
        if ($this->test($type, $value)) {
            return $this->next();
        }

        return null;
    }

    /**
     * @param int $number
     * @return Token
     * @throws SyntaxErrorException If the stream ends
     */
    public function lookAhead($number = 1)
    {
        if (!isset($this->tokens[$this->current + $number])) {
            throw new SyntaxErrorException('Unexpected end of stream');
        }

        return $this->tokens[$this->current + $number];
    }

    /**
     * @param int|int[] $type
     * @param string|string[] $value
     * @return Token
     * @throws SyntaxErrorException If the current token isn't expected
     */
    public function expect($type, $value = null)
    {
        $token = $this->getCurrent();
        if (!$this->test($type, $value)) {
            throw new SyntaxErrorException(
                sprintf(
                    'Unexpected token "%s" (%s) (%s)',
                    $token->getValue(),
                    $token->getName(),
                    $value === null ?
                        sprintf(
                            'expected %s',
                            implode(
                                '|',
                                array_map(
                                    function ($type) {
                                        return Token::getTypeName($type);
                                    },
                                    (array)$type
                                )
                            )
                        ) :
                        sprintf(
                            'expected %s (%s)',
                            implode(
                                '|',
                                array_map(
                                    function ($value) {
                                        return '"' . $value . '"';
                                    },
                                    (array)$type
                                )
                            ),
                            implode(
                                '|',
                                array_map(
                                    function ($type) {
                                        return Token::getTypeName($type);
                                    },
                                    (array)$type
                                )
                            )
                        )
                )
            );
        }
        $this->next();

        return $token;
    }

    /**
     * Checks whether is end of stream
     *
     * @return bool
     */
    public function isEnd()
    {
        return $this->tokens[$this->current]->getType() === Token::T_END;
    }

    /**
     * Gets the current token
     *
     * @return Token
     */
    public function getCurrent()
    {
        return $this->tokens[$this->current];
    }

    /**
     * @param int|int[] $type
     * @param string|string[] $value
     * @return bool
     */
    public function test($type, $value = null)
    {
        return  $this->tokens[$this->current]->test($type, $value);
    }
}
