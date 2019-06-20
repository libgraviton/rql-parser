<?php
namespace Graviton\RqlParserExample03;

use Graviton\RqlParser\Parser as BaseParser;
use Graviton\RqlParser\Query as BaseQuery;
use Graviton\RqlParser\QueryBuilder as BaseQueryBuilder;
use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Token;
use Graviton\RqlParser\NodeParserInterface;
use Graviton\RqlParser\TokenStream;
use Graviton\RqlParser\NodeParserChain;
use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\Node\SelectNode;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\EqNodeParser;
use Graviton\RqlParser\ValueParser\FieldParser;
use Graviton\RqlParser\ValueParser\ScalarParser;

require __DIR__ . '/../vendor/autoload.php';

/**
 * aggregate(fieldName)
 */
class AggregateFunctionNode extends AbstractNode
{
    private $function;
    private $field;

    public function __construct($function, $field)
    {
        $this->function = $function;
        $this->field = $field;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function getNodeName()
    {
        return $this->function;
    }

    public function __toString()
    {
        return sprintf('%s(%s)', $this->function, $this->field);
    }
}

/**
 * select(field1,count(field2),sum(field3),...)
 */
class SelectTokenParser implements NodeParserInterface
{
    private $allowedFunctions;

    public function __construct(array $allowedFunctions)
    {
        $this->allowedFunctions = $allowedFunctions;
    }

    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'select');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            if (($agregate = $tokenStream->nextIf(Token::T_OPERATOR, $this->allowedFunctions)) !== null) {
                $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

                $fields[] = new AggregateFunctionNode(
                    $agregate->getValue(),
                    $tokenStream->expect(Token::T_STRING)->getValue()
                );

                $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);
            } else {
                $fields[] = $tokenStream->expect(Token::T_STRING)->getValue();
            }

            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new SelectNode($fields);
    }

    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'select');
    }
}

/**
 * groupby(field1,field2,...)
 */
class GroupbyNode extends AbstractNode
{
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getNodeName()
    {
        return 'groupby';
    }
}

class GroupbyTokenParser implements NodeParserInterface
{
    public function parse(TokenStream $tokenStream)
    {
        $fields = [];

        $tokenStream->expect(Token::T_OPERATOR, 'groupby');
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        do {
            $fields[] = $tokenStream->expect(Token::T_STRING)->getValue();
            if (!$tokenStream->nextIf(Token::T_COMMA)) {
                break;
            }
        } while (true);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new GroupbyNode($fields);
    }

    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'groupby');
    }
}

/**
 * add supports for "groupby"
 */
class Query extends BaseQuery
{
    private $groupby;

    public function getGroupby()
    {
        return $this->groupby;
    }

    public function setGroupby(GroupbyNode $node)
    {
        $this->groupby = $node;
        return $this;
    }
}

class QueryBuilder extends BaseQueryBuilder
{
    public function __construct()
    {
        parent::__construct();
        $this->query = new Query();
    }

    public function addNode(AbstractNode $node)
    {
        if ($node instanceof GroupbyNode) {
            return $this->query->setGroupby($node);
        }

        return parent::addNode($node);
    }
}

class Parser extends BaseParser
{
    protected function createQueryBuilder()
    {
        return new QueryBuilder();
    }
}

$nodeParser = (new NodeParserChain())
    ->addNodeParser(new SelectTokenParser(['count', 'sum', 'avg', 'min', 'max']))
    ->addNodeParser(new GroupbyTokenParser())
    ->addNodeParser(new EqNodeParser(new FieldParser(), new ScalarParser()));


// parse
$lexer = new Lexer();
$parser = new Parser($nodeParser);

$tokenStream = $lexer->tokenize(implode('&', [
    'select(type,avg(age),min(age),max(age))',
    'eq(type,customer)',
    'groupby(type)',
]));
var_dump($parser->parse($tokenStream));
