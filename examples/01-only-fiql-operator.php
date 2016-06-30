<?php
namespace Xiag\Rql\ParserExample01;

use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;
use Xiag\Rql\Parser\NodeParserChain;
use Xiag\Rql\Parser\ValueParser;
use Xiag\Rql\Parser\TypeCaster;
use Xiag\Rql\Parser\NodeParser;

require __DIR__ . '/../vendor/autoload.php';

$scalarParser = (new ValueParser\ScalarParser())
    ->registerTypeCaster('string',  new TypeCaster\StringTypeCaster())
    ->registerTypeCaster('integer', new TypeCaster\IntegerTypeCaster())
    ->registerTypeCaster('float',   new TypeCaster\FloatTypeCaster())
    ->registerTypeCaster('boolean', new TypeCaster\BooleanTypeCaster());
$arrayParser = new ValueParser\ArrayParser($scalarParser);
$globParser = new ValueParser\GlobParser();
$fieldParser = new ValueParser\FieldParser();
$integerParser = new ValueParser\IntegerParser();

$queryNodeParser = new NodeParser\QueryNodeParser();
$queryNodeParser
    ->addNodeParser(new NodeParser\Query\GroupNodeParser($queryNodeParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\InNodeParser($fieldParser, $arrayParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\OutNodeParser($fieldParser, $arrayParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\EqNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\NeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\LtNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\GtNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\LeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\GeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\LikeNodeParser($fieldParser, $globParser));

$nodeParser = (new NodeParserChain())
    ->addNodeParser($queryNodeParser)
    ->addNodeParser(new NodeParser\SelectNodeParser($fieldParser))
    ->addNodeParser(new NodeParser\SortNodeParser($fieldParser))
    ->addNodeParser(new NodeParser\LimitNodeParser($integerParser));

$lexer = new Lexer();
$parser = new Parser($nodeParser);

// ok
$tokenStream = $lexer->tokenize('((a==true()|b!=str)&c>=10&d=in=(1,value,null()))');
var_dump($parser->parse($tokenStream));

// error
$tokenStream = $lexer->tokenize('or(eq(a,true),ne(b,str))&gte(c,10)&in(d,(1,value,null()))');
var_dump($parser->parse($tokenStream));
