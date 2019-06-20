<?php
namespace Graviton\RqlParserExample05;

use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Node;
use Graviton\RqlParser\Parser;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\SubLexerChain;
use Graviton\RqlParser\SubLexer;
use Graviton\RqlParser\SubLexerInterface;

require __DIR__ . '/../vendor/autoload.php';

class DateSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/(?<y>\d{4})-(?<m>\d{2})-(?<d>\d{2})/A', $code, $matches, null, $cursor)) {
            return null;
        }

        if (!checkdate($matches['m'], $matches['d'], $matches['y'])) {
            throw new SyntaxErrorException(sprintf('Invalid date value "%s"', $matches[0]));
        }

        return new Token(
            Token::T_DATE,
            $matches[0] . 'T00:00:00+00:00',
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}

$subLexer = (new SubLexerChain())
    ->addSubLexer(new SubLexer\ConstantSubLexer())
    ->addSubLexer(new SubLexer\PunctuationSubLexer())
    ->addSubLexer(new SubLexer\FiqlOperatorSubLexer())
    ->addSubLexer(new SubLexer\RqlOperatorSubLexer())
    ->addSubLexer(new SubLexer\TypeSubLexer())

    ->addSubLexer(new SubLexer\GlobSubLexer())
    ->addSubLexer(new SubLexer\StringSubLexer())
    ->addSubLexer(new SubLexer\DatetimeSubLexer())
    ->addSubLexer(new DateSubLexer())
    ->addSubLexer(new SubLexer\NumberSubLexer())

    ->addSubLexer(new SubLexer\SortSubLexer());

// parse
$lexer = new Lexer($subLexer);
$parser = new Parser();

$tokenStream = $lexer->tokenize('in(a,(2016-06-30,2016-06-30T09:12:33Z))');
var_dump($parser->parse($tokenStream));
