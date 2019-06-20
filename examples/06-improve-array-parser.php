<?php
namespace Graviton\RqlParserExample07;

use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Parser;
use Graviton\RqlParser\Node;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\InNodeParser;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\ValueParser;

require __DIR__ . '/../vendor/autoload.php';

class ArrayParser extends ValueParser\ArrayParser
{
    /**
     * @inheritdoc
     */
    public function parse(TokenStream $tokenStream)
    {
        if ($tokenStream->nextIf(Token::T_TYPE, 'array')) {
            $tokenStream->expect(Token::T_COLON);
            return [$this->itemParser->parse($tokenStream)];
        } else {
            return parent::parse($tokenStream);
        }
    }
}


$scalarParser = new ValueParser\ScalarParser();
$fieldNameParser = new ValueParser\FieldParser();
$arrayParser = new ArrayParser($scalarParser);

$nodeParser = new InNodeParser($fieldNameParser, $arrayParser);

// parse
$lexer = new Lexer();
$parser = new Parser($nodeParser);

$tokenStream = $lexer->tokenize(implode('&', [
    'in(a,(1,string,true()))',
    'in(b,array:1)',
]));
var_dump($parser->parse($tokenStream));
