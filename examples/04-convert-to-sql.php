<?php
namespace Graviton\RqlParserExample04;

use Graviton\RqlParser\Glob;
use Graviton\RqlParser\Query;
use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Node;
use Graviton\RqlParser\Parser;

require __DIR__ . '/../vendor/autoload.php';

// NOTE: this is just an example!
// NOTE: DO NOT USE it in production

class SqlBuilder
{
    private $select = [];
    private $where = '';
    private $sort = [];
    private $limit = 0;
    private $offset = 0;

    public function setSelect(array $fields)
    {
        $this->select = $fields;
        return $this;
    }

    public function setWhere($where)
    {
        $this->where = $where;
    }

    public function addRawWhere($where)
    {
        $this->where .= $where;
    }

    public function addWhereCondition($field, $value, $operator = '=')
    {
        $this->where .= sprintf(
            '%s %s %s',
            $this->encodeField($field),
            $operator,
            $this->encodeValue($value)
        );
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function setSort(array $fields)
    {
        $this->sort = $fields;
    }

    public function createQuery()
    {
        $result = [];

        if (empty($this->select)) {
            $result[] = 'SELECT *';
        } else {
            $result[] = 'SELECT ' . implode(', ', array_map([$this, 'encodeField'], $this->select));
        }

        $result[] = 'FROM `table`';

        if (!empty($this->where)) {
            $result[] = 'WHERE ' . $this->where;
        }

        if (!empty($this->sort)) {
            $result[] = 'ORDER BY ' . implode(
                    ', ',
                    array_map(
                        function ($field, $direction) {
                            return $this->encodeField($field) . ' ' . ($direction > 0 ? 'ASC' : 'DESC');
                        },
                        array_keys($this->sort),
                        array_values($this->sort)
                    )
                );
        }

        if (!empty($this->limit)) {
            $result[] = sprintf('LIMIT %d', $this->limit);

            if (!empty($this->offset)) {
                $result[] = sprintf('OFFSET %d', $this->offset);
            }
        }

        return implode(PHP_EOL, $result);
    }

    private function encodeField($field)
    {
        return '`' . $field . '`';
    }

    private function encodeValue($value)
    {
        if (is_array($value)) {
            return '(' . implode(',', array_map([$this, 'encodeValue'], $value)) . ')';
        }  elseif (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } elseif (is_null($value)) {
            return 'NULL';
        } elseif (is_float($value) || is_int($value)) {
            return (string) $value;
        } elseif (is_string($value)) {
            return var_export($value, true);
        } else {
            throw new \LogicException(sprintf('Invalid value "%s"', var_export($value, true)));
        }
    }
}

class SqlNodeVisitor
{
    public function visit(Query $query, SqlBuilder $sqlBuilder)
    {
        if ($query->getSelect() !== null) {
            $this->visitSelectNode($query->getSelect(), $sqlBuilder);
        }
        if ($query->getQuery() !== null) {
            $this->visitQueryNode($query->getQuery(), $sqlBuilder);
        }
        if ($query->getSort() !== null) {
            $this->visitSortNode($query->getSort(), $sqlBuilder);
        }
        if ($query->getLimit() !== null) {
            $this->visitLimitNode($query->getLimit(), $sqlBuilder);
        }
    }

    private function visitQueryNode(Node\AbstractQueryNode $node, SqlBuilder $sqlBuilder)
    {
        if ($node instanceof Node\Query\AbstractScalarOperatorNode) {
            $this->visitScalarNode($node, $sqlBuilder);
        } elseif ($node instanceof Node\Query\AbstractArrayOperatorNode) {
            $this->visitArrayNode($node, $sqlBuilder);
        } elseif ($node instanceof Node\Query\AbstractLogicalOperatorNode) {
            $this->visitLogicalNode($node, $sqlBuilder);
        } else {
            throw new \LogicException(sprintf('Unknown node "%s"', $node->getNodeName()));
        }
    }

    private function visitSelectNode(Node\SelectNode $node, SqlBuilder $sqlBuilder)
    {
        $sqlBuilder->setSelect($node->getFields());
    }

    private function visitSortNode(Node\SortNode $node, SqlBuilder $sqlBuilder)
    {
        $sqlBuilder->setSort($node->getFields());
    }

    private function visitLimitNode(Node\LimitNode $node, SqlBuilder $sqlBuilder)
    {
        $sqlBuilder->setLimit($node->getLimit());
        if ($node->getOffset() !== null) {
            $sqlBuilder->setOffset($node->getOffset());
        }
    }

    private function visitScalarNode(Node\Query\AbstractScalarOperatorNode $node, SqlBuilder $sqlBuilder)
    {
        static $operators = [
            'like'  => 'LIKE',

            'eq'    => '=',
            'ne'    => '<>',

            'lt'    => '<',
            'gt'    => '>',

            'le'    => '<=',
            'ge'    => '>=',
        ];

        if (!isset($operators[$node->getNodeName()])) {
            throw new \LogicException(sprintf('Unknown scalar node "%s"', $node->getNodeName()));
        }

        $value = $node->getValue();
        if ($value instanceof Glob) {
            $value = $value->toLike();
        } elseif ($value instanceof \DateTimeInterface) {
            $value = $value->format(DATE_ISO8601);
        }

        $sqlBuilder->addWhereCondition(
            $node->getField(),
            $value,
            $operators[$node->getNodeName()]
        );
    }

    private function visitArrayNode(Node\Query\AbstractArrayOperatorNode $node, SqlBuilder $sqlBuilder)
    {
        static $operators = [
            'in'  => 'IN',
            'out' => 'NOT IN',
        ];

        if (!isset($operators[$node->getNodeName()])) {
            throw new \LogicException(sprintf('Unknown array node "%s"', $node->getNodeName()));
        }

        $sqlBuilder->addWhereCondition(
            $node->getField(),
            $node->getValues(),
            $operators[$node->getNodeName()]
        );
    }

    private function visitLogicalNode(Node\Query\AbstractLogicalOperatorNode $node, SqlBuilder $sqlBuilder)
    {
        if ($node->getNodeName() === 'not') {
            $operator = ' AND ';
            $sqlBuilder->addRawWhere('NOT');
        } elseif ($node->getNodeName() === 'and') {
            $operator = ' AND ';
        } elseif ($node->getNodeName() === 'or') {
            $operator = ' OR ';
        } else {
            throw new \LogicException(sprintf('Unknown logical node "%s"', $node->getNodeName()));
        }

        $sqlBuilder->addRawWhere('(');
        foreach ($node->getQueries() as $index => $query) {
            $this->visitQueryNode($query, $sqlBuilder);
            if ($index !== count($node->getQueries()) - 1) {
                $sqlBuilder->addRawWhere($operator);
            }
        }
        $sqlBuilder->addRawWhere(')');
    }
}

// parse
$lexer = new Lexer();
$parser = new Parser();
$query = $parser->parse($lexer->tokenize(implode('&', [
    'select(a,b,c)',
    'ne(a,b)',
    '(not(c=d)|e=f)&out(c,(1,2))',
    'in(d,(true(),false(),null(),empty()))',
    'ge(e,2016-06-30T12:09:44Z)',
    'like(f,*sea?rch?)',
    'sort(-a,+b)',
    'limit(1,0)',
])));

// traversing
$sqlBuilder = new SqlBuilder();
$nodeVisitor = new SqlNodeVisitor();

$nodeVisitor->visit($query, $sqlBuilder);
var_dump($sqlBuilder->createQuery());
