<?php
namespace Xiag\Rql\ParserExample02;

use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\QueryBuilder;
use Xiag\Rql\Parser\ExpressionParser;
use Xiag\Rql\Parser\Token;
use Xiag\Rql\Parser\TokenParserGroup;
use Xiag\Rql\Parser\TokenStream;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\AbstractTokenParser;
use Xiag\Rql\Parser\TokenParser\Query\AbstractBasicTokenParser;
use Xiag\Rql\Parser\TokenParser\Query\Basic\ScalarOperator\EqTokenParser;

require __DIR__ . '/../vendor/autoload.php';

/**
 * AST node for aggregate function
 */
class AgregateFunctionNode extends AbstractNode
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
 * node "select(...)"
 */
class XSelectNode extends SelectNode
{
}

/**
 * parser for expression "select(field1,count(field2),sum(field3),...)"
 */
class SelectTokenParser extends AbstractTokenParser
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

                $fields[] = new AgregateFunctionNode(
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

        return new XSelectNode($fields);
    }

    public function supports(TokenStream $tokenStream)
    {
        return $tokenStream->test(Token::T_OPERATOR, 'select');
    }
}

/**
 * node "groupby(...)"
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

/**
 * parser for expression "groupby(field1,field2,...)"
 */
class GroupbyTokenParser extends AbstractTokenParser
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
 * node "between(...)"
 */
class BetweenNode extends AbstractQueryNode
{
    private $field;
    private $from;
    private $to;

    public function __construct($field, $from, $to)
    {
        $this->field = $field;
        $this->from = $from;
        $this->to = $to;
    }

    public function getField()
    {
        return $this->field;
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

/**
 * parser for expression "between(field,from,to)"
 */
class BetweenTokenParser extends AbstractBasicTokenParser
{
    public function parse(TokenStream $tokenStream)
    {
        $tokenStream->expect(Token::T_OPERATOR, $this->getOperatorName());
        $tokenStream->expect(Token::T_OPEN_PARENTHESIS);

        $field = $tokenStream->expect(Token::T_STRING)->getValue();
        $tokenStream->expect(Token::T_COMMA);
        $from = $this->getParser()->getExpressionParser()->parseScalar($tokenStream);
        $tokenStream->expect(Token::T_COMMA);
        $to = $this->getParser()->getExpressionParser()->parseScalar($tokenStream);

        $tokenStream->expect(Token::T_CLOSE_PARENTHESIS);

        return new BetweenNode($field, $from, $to);
    }

    public function getOperatorName()
    {
        return 'between';
    }
}

/**
 * add supports for "groupby"
 */
class XQuery extends Query
{
    protected $groupby;

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

class XQueryBuilder extends QueryBuilder
{
    public function __construct()
    {
        $this->query = new XQuery();
    }

    public function addNode(AbstractNode $node)
    {
        if ($node instanceof GroupbyNode) {
            return $this->query->setGroupby($node);
        }

        return parent::addNode($node);
    }
}

class XParser extends Parser
{
    protected function createQueryBuilder()
    {
        return new XQueryBuilder();
    }
}



// parse
$parser = (new XParser(new ExpressionParser()))
    ->addTokenParser(new SelectTokenParser(['count', 'sum', 'avg', 'min', 'max']))
    ->addTokenParser(
        (new TokenParserGroup())
            ->addTokenParser(new EqTokenParser())
            ->addTokenParser(new BetweenTokenParser())
    )
    ->addTokenParser(new GroupbyTokenParser());

$tokenStream = (new Lexer())->tokenize(implode('&', [
    'select(type,avg(age),min(age),max(age))',
    'eq(type,customer)&between(age,20,50)',
    'groupby(type)'
]));
var_dump($parser->parse($tokenStream));
