<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;

/**
 * Token stream
 */
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
    public function count()
    {
        return count($this->tokens);
    }

    /**
     * @return Token
     * @throws SyntaxErrorException
     */
    public function next()
    {
        if (!isset($this->tokens[++$this->current])) {
            throw new SyntaxErrorException('Unexpected end of stream');
        }

        return $this->tokens[$this->current - 1];
    }

    /**
     * @param int $type
     * @param string $value
     * @return Token|null
     */
    public function nextIf($type, $value = null)
    {
        if ($this->tokens[$this->current]->test($type, $value)) {
            return $this->next();
        }

        return null;
    }

    /**
     * @param int $number
     * @return Token
     * @throws SyntaxErrorException
     */
    public function lookAhead($number = 1)
    {
        if (!isset($this->tokens[$this->current + $number])) {
            throw new SyntaxErrorException('Unexpected end of stream');
        }

        return $this->tokens[$this->current + $number];
    }

    /**
     * @param int $type
     * @param string $value
     * @return Token
     * @throws SyntaxErrorException
     */
    public function expect($type, $value = null)
    {
        $token = $this->tokens[$this->current];
        if (!$token->test($type, $value)) {
            throw new SyntaxErrorException(
                sprintf(
                    'Unexpected token "%s" (%s) (%s)',
                    $token->getValue(),
                    $token->getName(),
                    $value === null ?
                        sprintf('expected %s', Token::getTypeName($type)) :
                        sprintf('expected "%s" (%s)', $value, Token::getTypeName($type))
                )
            );
        }
        $this->next();

        return $token;
    }

    /**
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
     * @param int $type
     * @param string|array $value
     * @return bool
     */
    public function test($type, $value = null)
    {
        return  $this->tokens[$this->current]->test($type, $value);
    }
}
