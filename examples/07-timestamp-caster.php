<?php
namespace Graviton\RqlParserExample07;

use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Parser;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\TypeCasterInterface;
use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\EqNodeParser;
use Graviton\RqlParser\ValueParser;

require __DIR__ . '/../vendor/autoload.php';

class TimestampCaster implements TypeCasterInterface
{
    /**
     * @inheritdoc
     */
    public function typeCast(Token $token)
    {
        if (!$token->test(Token::T_INTEGER)) {
            throw new SyntaxErrorException('Timestamp type caster expects an integer token');
        }
        return new \DateTime('@' . $token->getValue());
    }
}


$scalarParser = (new ValueParser\ScalarParser())
    ->registerTypeCaster('timestamp', new TimestampCaster());
$fieldNameParser = new ValueParser\FieldParser();

$nodeParser = new EqNodeParser($fieldNameParser, $scalarParser);

// parse
$lexer = new Lexer();
$parser = new Parser($nodeParser);

$tokenStream = $lexer->tokenize('eq(a,timestamp:1444000000)');
var_dump($parser->parse($tokenStream));
