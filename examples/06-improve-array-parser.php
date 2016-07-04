<?php
namespace Xiag\Rql\ParserExample07;

use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;
use Xiag\Rql\Parser\Node;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\InNodeParser;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\ValueParser;

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
