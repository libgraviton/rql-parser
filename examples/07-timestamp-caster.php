<?php
namespace Xiag\Rql\ParserExample07;

use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TypeCasterInterface;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\EqNodeParser;
use Xiag\Rql\Parser\ValueParser;

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
