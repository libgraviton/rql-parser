<?php
namespace Graviton\RqlParserExample05;

use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Node;
use Graviton\RqlParser\Parser;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\SubParserInterface;
use Graviton\RqlParser\NodeParserChain;
use Graviton\RqlParser\SubLexerChain;
use Graviton\RqlParser\SubLexer;
use Graviton\RqlParser\SubLexerInterface;
use Graviton\RqlParser\NodeParser;
use Graviton\RqlParser\TypeCaster;
use Graviton\RqlParser\ValueParser;
use Graviton\RqlParser\NodeParser\Query\LogicalOperator;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator;

require __DIR__ . '/../vendor/autoload.php';

const T_IDENTIFIER = 9999;
Token::$typeNameMap[T_IDENTIFIER] = 'T_IDENTIFIER';


/**
 * lex T_IDENTIFIER token
 */
class IdentifierSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/[a-z][a-z0-9]*(?:\.[a-z][a-z0-9]*)*/Ai', $code, $matches, null, $cursor)) {
            return null;
        }

        return new Token(
            T_IDENTIFIER,
            $matches[0],
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}

/**
 * quoted string
 */
class StringSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        if (!preg_match('/"(?:\\\\"|.)*?"/A', $code, $matches, null, $cursor)) {
            return null;
        }

        return new Token(
            Token::T_STRING,
            strtr(substr($matches[0], 1, -1), ['\\"' => '"']),
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}

/**
 * now we don't need to support empty(). just use "" (empty string)
 */
class ConstantSubLexer implements SubLexerInterface
{
    /**
     * @inheritdoc
     */
    public function getTokenAt($code, $cursor)
    {
        static $types = [
            'true' => Token::T_TRUE,
            'false' => Token::T_FALSE,
            'null' => Token::T_NULL,
        ];

        if (!preg_match('/(?:true|false|null)/A', $code, $matches, null, $cursor)) {
            return null;
        }

        return new Token(
            $types[$matches[0]],
            $matches[0],
            $cursor,
            $cursor + strlen($matches[0])
        );
    }
}

/**
 * we will use T_IDENTIFIER as a field name
 */
class IdentifierParser implements SubParserInterface
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        return $tokenStream->expect(T_IDENTIFIER)->getValue();
    }
}

/**
 * get rid of type casters
 */
class ScalarParser implements SubParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(TokenStream $tokenStream)
    {
        $token = $tokenStream->getCurrent();
        if ($tokenStream->nextIf(Token::T_FALSE)) {
            return false;
        } elseif ($tokenStream->nextIf(Token::T_TRUE)) {
            return true;
        } elseif ($tokenStream->nextIf(Token::T_NULL)) {
            return null;
        } elseif ($tokenStream->nextIf(Token::T_DATE)) {
            return new \DateTime($token->getValue());
        } elseif ($tokenStream->nextIf(Token::T_STRING)) {
            return $token->getValue();
        } elseif ($tokenStream->nextIf(Token::T_INTEGER)) {
            return (int)$token->getValue();
        } elseif ($tokenStream->nextIf(Token::T_FLOAT)) {
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


$subLexer = (new SubLexerChain())
    ->addSubLexer(new SubLexer\PunctuationSubLexer())
    ->addSubLexer(new SubLexer\FiqlOperatorSubLexer())
    ->addSubLexer(new SubLexer\RqlOperatorSubLexer())

    ->addSubLexer(new ConstantSubLexer())
    ->addSubLexer(new StringSubLexer())
    ->addSubLexer(new IdentifierSubLexer())
    ->addSubLexer(new SubLexer\DatetimeSubLexer())
    ->addSubLexer(new SubLexer\NumberSubLexer())

    ->addSubLexer(new SubLexer\SortSubLexer());


$identifierParser = new IdentifierParser();
$scalarParser = new ScalarParser();
$arrayParser = new ValueParser\ArrayParser($scalarParser);
$integerParser = new ValueParser\IntegerParser();

$queryNodeParser = new NodeParser\QueryNodeParser();
$queryNodeParser
    ->addNodeParser(new NodeParser\Query\GroupNodeParser($queryNodeParser))

    ->addNodeParser(new LogicalOperator\AndNodeParser($queryNodeParser))
    ->addNodeParser(new LogicalOperator\OrNodeParser($queryNodeParser))
    ->addNodeParser(new LogicalOperator\NotNodeParser($queryNodeParser))

    ->addNodeParser(new ComparisonOperator\Rql\InNodeParser($identifierParser, $arrayParser))
    ->addNodeParser(new ComparisonOperator\Rql\OutNodeParser($identifierParser, $arrayParser))
    ->addNodeParser(new ComparisonOperator\Rql\EqNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Rql\NeNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Rql\LtNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Rql\GtNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Rql\LeNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Rql\GeNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Rql\LikeNodeParser($identifierParser, $scalarParser))

    ->addNodeParser(new ComparisonOperator\Fiql\InNodeParser($identifierParser, $arrayParser))
    ->addNodeParser(new ComparisonOperator\Fiql\OutNodeParser($identifierParser, $arrayParser))
    ->addNodeParser(new ComparisonOperator\Fiql\EqNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Fiql\NeNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Fiql\LtNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Fiql\GtNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Fiql\LeNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Fiql\GeNodeParser($identifierParser, $scalarParser))
    ->addNodeParser(new ComparisonOperator\Fiql\LikeNodeParser($identifierParser, $scalarParser));

$nodeParser = (new NodeParserChain())
    ->addNodeParser($queryNodeParser)
    ->addNodeParser(new NodeParser\SelectNodeParser($identifierParser))
    ->addNodeParser(new NodeParser\SortNodeParser($identifierParser))
    ->addNodeParser(new NodeParser\LimitNodeParser($integerParser));

// parse
$lexer = new Lexer($subLexer);
$parser = new Parser($nodeParser);

$tokenStream = $lexer->tokenize(implode('&', [
    'select(a.b,c.d)',
    'ne(a.b,"quoted string !@#$%^&*()_+[]{} ;:\'\"\| ,<.>/? ~ + escaped \" double quote")',
    '(not(c.d="d")|e=false)',
    'out(f,(1,"2",null,2016-06-29T23:30:33Z,true))',
    'sort(-a.b,+c.d)',
    'limit(1,2)',
]));
var_dump($parser->parse($tokenStream));
