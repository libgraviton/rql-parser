<?php
namespace Graviton\RqlParserExample02;

use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Parser;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\SubParserInterface;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParserChain;
use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\Node\Query\AbstractComparisonOperatorNode;
use Graviton\RqlParser\Exception\SyntaxErrorException;
use Graviton\RqlParser\ValueParser;
use Graviton\RqlParser\NodeParser;
use Graviton\RqlParser\TypeCaster;

require __DIR__ . '/../vendor/autoload.php';

/**
 * between(field,from,to)
 */
class BetweenNode extends AbstractComparisonOperatorNode
{
    private $from;
    private $to;

    public function __construct($field, $from, $to)
    {
        $this->field = $field;
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getNodeName()
    {
        return 'between';
    }
}

class BetweenTokenParser implements NodeParserInterface
{
    private $valueParser;

    public function __construct(SubParserInterface $valueParser)
    {
        $this->valueParser = $valueParser;
    }

    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, 'between');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_COMMA);
        $from = $this->valueParser->parse($tokenStream);
        $tokenStream->expect(Token::T_COMMA);
        $to = $this->valueParser->parse($tokenStream);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new BetweenNode($field, $from, $to);
    }

    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'between');
    }
}

/**
 * elemMatch(field,query)
 */
class ElemMatchNode extends AbstractComparisonOperatorNode
{
    private $query;

    public function __construct($field, AbstractQueryNode $query)
    {
        $this->field = $field;
        $this->query = $query;
    }

    public function getNodeName()
    {
        return 'elemMatch';
    }

    public function getQuery()
    {
        return $this->query;
    }
    public function setQuery(AbstractQueryNode $query)
    {
        $this->query = $query;
    }
}

class ElemMatchNodeParser implements NodeParserInterface
{
    private $queryParser;

    public function __construct(SubParserInterface $queryParser)
    {
        $this->queryParser = $queryParser;
    }

    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'elemMatch');
    }

    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, 'elemMatch');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_COMMA);

        $query = $this->queryParser->parse($tokenStream);
        if (!$query instanceof AbstractQueryNode) {
            throw new SyntaxErrorException(
                sprintf(
                    '"elemMatch" operator expects parameter "query" to be instance of "%s", "%s" given',
                    AbstractQueryNode::class,
                    get_class($query)
                )
            );
        }

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);
        return new ElemMatchNode($field, $query);
    }
}

// create node parser
$scalarParser = (new ValueParser\ScalarParser())
    ->registerTypeCaster('string', new TypeCaster\StringTypeCaster())
    ->registerTypeCaster('integer', new TypeCaster\IntegerTypeCaster())
    ->registerTypeCaster('float', new TypeCaster\FloatTypeCaster())
    ->registerTypeCaster('boolean', new TypeCaster\BooleanTypeCaster());
$arrayParser = new ValueParser\ArrayParser($scalarParser);
$globParser = new ValueParser\GlobParser();
$fieldParser = new ValueParser\FieldParser();
$integerParser = new ValueParser\IntegerParser();

$queryNodeParser = new NodeParser\QueryNodeParser();
$queryNodeParser
    ->addNodeParser(new NodeParser\Query\GroupNodeParser($queryNodeParser))

    ->addNodeParser(new NodeParser\Query\LogicalOperator\AndNodeParser($queryNodeParser))
    ->addNodeParser(new NodeParser\Query\LogicalOperator\OrNodeParser($queryNodeParser))
    ->addNodeParser(new NodeParser\Query\LogicalOperator\NotNodeParser($queryNodeParser))

    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\InNodeParser($fieldParser, $arrayParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\OutNodeParser($fieldParser, $arrayParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\EqNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\NeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\LtNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\GtNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\LeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\GeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Rql\LikeNodeParser($fieldParser, $globParser))

    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\InNodeParser($fieldParser, $arrayParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\OutNodeParser($fieldParser, $arrayParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\EqNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\NeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\LtNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\GtNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\LeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\GeNodeParser($fieldParser, $scalarParser))
    ->addNodeParser(new NodeParser\Query\ComparisonOperator\Fiql\LikeNodeParser($fieldParser, $globParser))

    ->addNodeParser(new BetweenTokenParser($scalarParser))
    ->addNodeParser(new ElemMatchNodeParser($queryNodeParser));

$nodeParser = (new NodeParserChain())
    ->addNodeParser($queryNodeParser)
    ->addNodeParser(new NodeParser\SelectNodeParser($fieldParser))
    ->addNodeParser(new NodeParser\SortNodeParser($fieldParser))
    ->addNodeParser(new NodeParser\LimitNodeParser($integerParser));

// parse
$lexer = new Lexer();
$parser = new Parser($nodeParser);

$tokenStream = $lexer->tokenize('between(x,1,2)&elemMatch(array,(between(x,3,4)&a=b&(c!=d|e!=f)&not(le(g,h))))');
var_dump($parser->parse($tokenStream));
