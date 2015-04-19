<?php
namespace Mrix\Rql\Parser;

use Mrix\Rql\Parser\Exception\SyntaxErrorException;

/**
 */
class Lexer
{
    const REGEX_VALUE       = '/(\w|\-|\+|\*|\$|\:|\.|\%[0-9a-f]{2})+/Ai';
    const REGEX_OPERATOR    = '/(?:[a-z]\w*(?=\()|\=[a-z]\w*\=|==|!=|<>|>=|<=|<|>|==|=)/Ai';
    const REGEX_TYPE        = '/[a-z]\w*\:/Ai';
    const REGEX_PUNCTUATION = '/[\(\)&,|]/A';
    const REGEX_CONSTANT    = '/(null|empty|true|false)\(\)/A';

    /**
     * @var array
     */
    protected $tokens;
    /**
     * @var string
     */
    protected $code;
    /**
     * @var int
     */
    protected $cursor;
    /**
     * @var int
     */
    protected $end;

    /**
     * @param string $code
     * @return TokenStream
     * @throws SyntaxErrorException
     */
    public function tokenize($code)
    {
        $this->code     = $code;
        $this->cursor   = 0;
        $this->end      = strlen($this->code);
        $this->tokens   = [];

        while ($this->cursor < $this->end) {
            $this->lexExpression();
        }
        $this->pushToken(Token::T_END, '');

        return new TokenStream($this->tokens);
    }

    /**
     * @param int $type
     * @param string $value
     * @return void
     */
    protected function pushToken($type, $value)
    {
        $this->tokens[] = new Token($type, $value, $this->cursor);
    }

    /**
     * @param string $text
     * @return void
     */
    protected function moveCursor($text)
    {
        $this->cursor += strlen($text);
    }

    /**
     * @param string $regex
     * @return string|bool
     */
    protected function isMatch($regex)
    {
        return preg_match($regex, $this->code, $match, null, $this->cursor) ? $match[0] : false;
    }

    protected function lexExpression()
    {
        if (($match = $this->isMatch(static::REGEX_CONSTANT)) !== false) {
            $this->processConstant($match);
        } elseif (($match = $this->isMatch(static::REGEX_OPERATOR)) !== false) {
            $this->processOperator($match);
        } elseif (($match = $this->isMatch(static::REGEX_TYPE)) !== false) {
            $this->processType($match);
        } elseif (($match = $this->isMatch(static::REGEX_PUNCTUATION)) !== false) {
            $this->processPunctuation($match);
        } elseif (($match = $this->isMatch(static::REGEX_VALUE)) !== false) {
            $this->processValue($match);
        } else {
            throw new SyntaxErrorException(
                sprintf(
                    'Invalid character "%s" at position %d',
                    $this->code[$this->cursor],
                    $this->cursor
                )
            );
        }
    }

    protected function processPunctuation($punct)
    {
        if ($punct === '&') {
            $this->pushToken(Token::T_AMPERSAND, $punct);
        } elseif ($punct === '|') {
            $this->pushToken(Token::T_VERTICAL_BAR, $punct);
        } elseif ($punct === ',') {
            $this->pushToken(Token::T_COMMA, $punct);
        } elseif ($punct === '(') {
            $this->pushToken(Token::T_OPEN_PARENTHESIS, $punct);
        } elseif ($punct === ')') {
            $this->pushToken(Token::T_CLOSE_PARENTHESIS, $punct);
        } else {
            throw new SyntaxErrorException(sprintf('Invalid punctuation symbol "%s"', $punct));
        }

        $this->moveCursor($punct);
    }

    protected function processConstant($constant)
    {
        if ($constant === 'null()') {
            $this->pushToken(Token::T_NULL, $constant);
        } elseif ($constant === 'empty()') {
            $this->pushToken(Token::T_EMPTY, $constant);
        } elseif ($constant === 'true()') {
            $this->pushToken(Token::T_TRUE, $constant);
        } elseif ($constant === 'false()') {
            $this->pushToken(Token::T_FALSE, $constant);
        } else {
            throw new SyntaxErrorException(sprintf('Invalid constant "%s"', $constant));
        }

        $this->moveCursor($constant);
    }

    protected function processOperator($operator)
    {
        static $operatorMap = [
            '='     => 'eq',
            '=='    => 'eq',

            '!='    => 'ne',
            '<>'    => 'ne',

            '>'     => 'gt',
            '<'     => 'lt',

            '>='    => 'ge',
            '<='    => 'le',
        ];

        if (isset($operatorMap[$operator])) {
            $decoded = $operator;
        } elseif ($operator[0] === '=') {
            $decoded = substr($operator, 1, -1);
        } else {
            $decoded = $operator;
        }

        $this->pushToken(Token::T_OPERATOR, $decoded);
        $this->moveCursor($operator);
    }

    protected function processType($type)
    {
        $this->pushToken(Token::T_TYPE, substr($type, 0, -1));
        $this->moveCursor($type);
    }

    protected function processValue($value)
    {
        $raw = $value;

        $this->pushToken($this->detectValueType($value), $value);
        $this->moveCursor($raw);
    }

    protected function detectValueType(&$value)
    {
        if ($value === 'true') {
            return Token::T_TRUE;
        } elseif ($value === 'false') {
            return Token::T_FALSE;
        } elseif ($value === 'null') {
            return Token::T_NULL;
        } elseif (is_numeric($value)) {
            return strpos($value, '.') !== false || strpos($value, 'e') !== false || strpos($value, 'E') !== false ?
                Token::T_FLOAT :
                Token::T_INTEGER;
        } elseif (
            strlen($value) === 10 && ctype_digit($value[0]) && strpos($value, '-') === 4 &&
            preg_match('/^(?<y>\d{4})-(?<m>\d{2})-(?<d>\d{2})$/', $value, $matches) &&
            checkdate($matches['m'], $matches['d'], $matches['y'])
        ) {
            return Token::T_DATE;
        } elseif (
            strlen($value) === 20 && ctype_digit($value[0]) && strpos($value, '-') === 4 && strpos($value, ':') === 13 &&
            preg_match('/^(?<y>\d{4})-(?<m>\d{2})-(?<d>\d{2})T(?<h>\d{2}):(?<i>\d{2}):(?<s>\d{2})Z$/', $value, $matches) &&
            checkdate($matches['m'], $matches['d'], $matches['y']) &&
            $matches['h'] < 24 && $matches['i'] < 60 && $matches['s'] < 60
        ) {
            return Token::T_DATE;
        } else {
            $value = rawurldecode($value);
            return Token::T_STRING;
        }
    }
}
