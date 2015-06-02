<?php
namespace Mrix\Rql\ParserExample01;

use Mrix\Rql\Parser\Lexer;
use Mrix\Rql\Parser\Parser;
use Mrix\Rql\Parser\ExpressionParser;
use Mrix\Rql\Parser\TokenParserGroup;
use Mrix\Rql\Parser\TokenParser\Query\GroupTokenParser;
use Mrix\Rql\Parser\TokenParser\Query\Fiql;

require __DIR__ . '/../vendor/autoload.php';

$queryTokenParser = new TokenParserGroup();
$queryTokenParser
    ->addTokenParser(new GroupTokenParser($queryTokenParser))
    ->addTokenParser(new Fiql\ArrayOperator\InTokenParser())
    ->addTokenParser(new Fiql\ArrayOperator\OutTokenParser())
    ->addTokenParser(new Fiql\ScalarOperator\EqTokenParser())
    ->addTokenParser(new Fiql\ScalarOperator\NeTokenParser())
    ->addTokenParser(new Fiql\ScalarOperator\LtTokenParser())
    ->addTokenParser(new Fiql\ScalarOperator\GtTokenParser())
    ->addTokenParser(new Fiql\ScalarOperator\LeTokenParser())
    ->addTokenParser(new Fiql\ScalarOperator\GeTokenParser());

$parser = new Parser(new ExpressionParser());
$parser->addTokenParser($queryTokenParser);

$lexer = new Lexer();

// ok
$tokenStream = $lexer->tokenize('((a==true|b!=str)&c>=10&d=in=(1,value,null))');
var_dump($parser->parse($tokenStream));

// error
$tokenStream = $lexer->tokenize('or(eq(a,true),ne(b,str))&gte(c,10)&in(d,(1,value,null))');
var_dump($parser->parse($tokenStream));
