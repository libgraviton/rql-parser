<?php
namespace Graviton\RqlParserTests;

use PHPUnit\Framework\TestCase;
use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Parser;
use Graviton\RqlParser\Query;
use Graviton\RqlParser\QueryBuilder;
use Graviton\RqlParser\Node;
use Graviton\RqlParser\Glob;
use Graviton\RqlParser\Exception\SyntaxErrorException;

class ParserTest extends TestCase
{
    /**
     * @param string $rql
     * @param Query $expected
     * @param bool $explicitComparison if false, we don't fully compare backwards (only for long integers)
     * @return void
     *
     * @dataProvider dataParse()
     */
    public function testParse($rql, Query $expected, $explicitComparison = true)
    {
        $lexer = new Lexer();
        $parser = new Parser();

        // see if the query parses as expected
        $query = $parser->parse($lexer->tokenize($rql));

        $this->assertEquals(
            $expected,
            $query
        );

        // now we call toRql() on the query -> parse that again -> see if it's indeed the same query
        if ($explicitComparison) {
            $backToRql = $query->toRql();
            $backToRqlQuery = $parser->parse($lexer->tokenize($backToRql));

            $this->assertEquals(
                $expected,
                $backToRqlQuery
            );
        }
    }

    /**
     * @param string $rql
     * @param string $exceptionMessage
     * @return void
     *
     * @dataProvider dataSyntaxError()
     */
    public function testSyntaxError($rql, $exceptionMessage)
    {
        $this->expectException(SyntaxErrorException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $lexer = new Lexer();
        $parser = new Parser();

        $parser->parse($lexer->tokenize($rql));
    }

    /**
     * @return array
     */
    public function dataParse()
    {
        return [
            'simple eq' => [
                'eq(name,value)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('name', 'value'))
                    ->getQuery(),
            ],
            'scalar operators' => [
                'eq(a,1)&ne(b,2)&lt(c,3)&gt(d,4)&le(e,5)&ge(f,6)&like(g,*abc?)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 1))
                    ->addQuery(new Node\Query\ScalarOperator\NeNode('b', 2))
                    ->addQuery(new Node\Query\ScalarOperator\LtNode('c', 3))
                    ->addQuery(new Node\Query\ScalarOperator\GtNode('d', 4))
                    ->addQuery(new Node\Query\ScalarOperator\LeNode('e', 5))
                    ->addQuery(new Node\Query\ScalarOperator\GeNode('f', 6))
                    ->addQuery(new Node\Query\ScalarOperator\LikeNode('g', new Glob('*abc?')))
                    ->getQuery(),
            ],
            'array operators' => [
                'in(a,(1,b))&out(c,(2,d))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [1, 'b']))
                    ->addQuery(new Node\Query\ArrayOperator\OutNode('c', [2, 'd']))
                    ->getQuery(),
            ],
            'multiple query operators' => [
                'eq(a,b)&lt(c,d)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 'b'))
                    ->addQuery(new Node\Query\ScalarOperator\LtNode('c', 'd'))
                    ->getQuery(),
            ],
            'logic operators' => [
                'and(eq(a,b),lt(c,d),or(in(a,(1,f)),gt(g,2)))&not(ne(h,3))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\LogicalOperator\AndNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                        new Node\Query\LogicalOperator\OrNode([
                            new Node\Query\ArrayOperator\InNode('a', [1, 'f']),
                            new Node\Query\ScalarOperator\GtNode('g', 2),
                        ])
                    ]))
                    ->addQuery(new Node\Query\LogicalOperator\NotNode([
                        new Node\Query\ScalarOperator\NeNode('h', 3),
                    ]))
                    ->getQuery(),
            ],

            'select, sort and limit operators' => [
                'select(a,b,c)&sort(+a,-b)&limit(1,2)',
                (new QueryBuilder())
                    ->addSelect(new Node\SelectNode(['a', 'b', 'c']))
                    ->addSort(new Node\SortNode([
                        'a' => Node\SortNode::SORT_ASC,
                        'b' => Node\SortNode::SORT_DESC,
                    ]))
                    ->addLimit(new Node\LimitNode(1, 2))
                    ->getQuery(),
            ],

            'string typecast' => [
                'ne(x,string:*)&' .
                'eq(a,string:3)&' .
                'in(b,(string:true(),string:false(),string:null(),string:empty()))&' .
                'out(c,(string:-1,string:+.5e10))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\NeNode('x', '*'))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', '3'))
                    ->addQuery(new Node\Query\ArrayOperator\InNode('b', [
                        'true',
                        'false',
                        'null',
                        '',
                    ]))
                    ->addQuery(new Node\Query\ArrayOperator\OutNode('c', [
                        '-1',
                        '+.5e10'
                    ]))
                    ->getQuery(),
            ],
            'integer typecast' => [
                'eq(a,integer:0)&' .
                'eq(b,integer:1.5)&' .
                'eq(c,integer:null())&' .
                'eq(d,integer:true())&' .
                'eq(e,integer:a)&' .
                'eq(f,integer:empty())' .
                'eq(g,integer:false())&' .
                'eq(h,integer:2016-07-01T09:48:55Z)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('b', 1))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('c', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('d', 1))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('e', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('f', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('g', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('h', 20160701094855))
                    ->getQuery(),
            ],
            'float typecast' => [
                'eq(a,float:0)&' .
                'eq(b,float:1.5)&' .
                'eq(c,float:null())&' .
                'eq(d,float:true())&' .
                'eq(e,float:a)&' .
                'eq(f,float:empty())' .
                'eq(g,float:false())&' .
                'eq(h,float:2016-07-01T09:48:55Z)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('b', 1.5))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('c', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('d', 1.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('e', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('f', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('g', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('h', 20160701094855.))
                    ->getQuery(),
            ],
            'boolean typecast' => [
                'eq(a,boolean:0)&' .
                'eq(b,boolean:1.5)&' .
                'eq(c,boolean:null())&' .
                'eq(d,boolean:true())&' .
                'eq(e,boolean:a)&' .
                'eq(f,boolean:empty())' .
                'eq(g,boolean:false())&' .
                'eq(h,boolean:2016-07-01T09:48:55Z)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', false))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('b', true))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('c', false))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('d', true))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('e', true))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('f', false))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('g', false))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('h', true))
                    ->getQuery(),
            ],
            'glob typecast' => [
                'like(a,glob:0)&' .
                'like(b,glob:1.5)&' .
                'like(c,glob:a)&' .
                'like(d,glob:2016-07-01T09:48:55Z)',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\LikeNode('a', new Glob('0')))
                    ->addQuery(new Node\Query\ScalarOperator\LikeNode('b', new Glob('1.5')))
                    ->addQuery(new Node\Query\ScalarOperator\LikeNode('c', new Glob('a')))
                    ->addQuery(new Node\Query\ScalarOperator\LikeNode('d', new Glob('2016-07-01T09:48:55+0000')))
                    ->getQuery(),
            ],
            'constants' => [
                'in(a,(null(),true(),false(),empty()))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        null,
                        true,
                        false,
                        '',
                    ]))
                    ->getQuery(),
            ],
            'fiql operators' => [
                'a=eq=1&b=ne=2&c=lt=3&d=gt=4&e=le=5&f=ge=6&g=in=(7,8)&h=out=(9,10)&i=like=*abc?',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 1))
                    ->addQuery(new Node\Query\ScalarOperator\NeNode('b', 2))
                    ->addQuery(new Node\Query\ScalarOperator\LtNode('c', 3))
                    ->addQuery(new Node\Query\ScalarOperator\GtNode('d', 4))
                    ->addQuery(new Node\Query\ScalarOperator\LeNode('e', 5))
                    ->addQuery(new Node\Query\ScalarOperator\GeNode('f', 6))
                    ->addQuery(new Node\Query\ArrayOperator\InNode('g', [7, 8]))
                    ->addQuery(new Node\Query\ArrayOperator\OutNode('h', [9, 10]))
                    ->addQuery(new Node\Query\ScalarOperator\LikeNode('i', new Glob('*abc?')))
                    ->getQuery(),
            ],
            'fiql operators (json compatible)' => [
                'a=1&b==2&c<>3&d!=4&e<5&f>6&g<=7&h>=8',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 1))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('b', 2))
                    ->addQuery(new Node\Query\ScalarOperator\NeNode('c', 3))
                    ->addQuery(new Node\Query\ScalarOperator\NeNode('d', 4))
                    ->addQuery(new Node\Query\ScalarOperator\LtNode('e', 5))
                    ->addQuery(new Node\Query\ScalarOperator\GtNode('f', 6))
                    ->addQuery(new Node\Query\ScalarOperator\LeNode('g', 7))
                    ->addQuery(new Node\Query\ScalarOperator\GeNode('h', 8))
                    ->getQuery(),
            ],
            'group with one operator' => [
                '(eq(a,b))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 'b'))
                    ->getQuery(),
            ],
            'simple groups' => [
                '(eq(a,b)&lt(c,d))&(ne(e,f)|gt(g,h))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\LogicalOperator\AndNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                    ]))
                    ->addQuery(new Node\Query\LogicalOperator\OrNode([
                        new Node\Query\ScalarOperator\NeNode('e', 'f'),
                        new Node\Query\ScalarOperator\GtNode('g', 'h'),
                    ]))
                    ->getQuery(),
            ],
            'deep groups & mix groups with operators' => [
                '(eq(a,b)|lt(c,d)|and(gt(e,f),(ne(g,h)|ge(i,j)|in(k,(l,m,n))|(o<>p&q=le=r))))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\LogicalOperator\OrNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                        new Node\Query\LogicalOperator\AndNode([
                            new Node\Query\ScalarOperator\GtNode('e', 'f'),
                            new Node\Query\LogicalOperator\OrNode([
                                new Node\Query\ScalarOperator\NeNode('g', 'h'),
                                new Node\Query\ScalarOperator\GeNode('i', 'j'),
                                new Node\Query\ArrayOperator\InNode('k', ['l', 'm', 'n']),
                                new Node\Query\LogicalOperator\AndNode([
                                    new Node\Query\ScalarOperator\NeNode('o', 'p'),
                                    new Node\Query\ScalarOperator\LeNode('q', 'r'),
                                ]),
                            ]),
                        ]),
                    ]))
                    ->getQuery(),
            ],
            'datetime support' => [
                'in(a,(2015-04-16T17:40:32Z,2012-02-29T17:40:32+0000))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        new \DateTime('2015-04-16T17:40:32+0000'),
                        new \DateTime('2012-02-29T17:40:32+0000'),
                    ]))
                    ->getQuery(),
            ],

            'datetime timezone support' => [
                'in(a,(2019-01-01T00:00:00+0200,2019-12-01T00:00:00+0600,2019-06-01T00:00:00-0600))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        new \DateTime('2019-01-01T00:00:00+0200'),
                        new \DateTime('2019-12-01T00:00:00+0600'),
                        new \DateTime('2019-06-01T00:00:00-0600'),
                    ]))
                    ->getQuery(),
            ],

            'datetime z timezone support' => [
                'in(a,(2019-01-01T00:00:00Z))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        new \DateTime('2019-01-01T00:00:00+0000')
                    ]))
                    ->getQuery(),
            ],

            'string encoding' => [
                vsprintf('in(a,(%s,%s,%s,%s,%s,%s,%s))&like(b,%s)&eq(c,%s)', [
                    $this->encodeString('+a-b:c'),
                    'null()',
                    $this->encodeString('null()'),
                    '2015-04-19T21:00:00+0000',
                    $this->encodeString('2015-04-19T21:00:00+0000'),
                    '1.1e+3',
                    $this->encodeString('1.1e+3'),
                    '*abc?',
                    $this->encodeString('*abc?'),
                ]),
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        '+a-b:c',
                        null,
                        'null()',
                        new \DateTime('2015-04-19T21:00:00+0000'),
                        '2015-04-19T21:00:00+0000',
                        1.1e+3,
                        '1.1e+3',
                    ]))
                    ->addQuery(new Node\Query\ScalarOperator\LikeNode('b', new Glob('*abc?')))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('c', '*abc?'))
                    ->getQuery(),
            ],
            'long integers' => [
                vsprintf('in(a,(%s,%s,%s,%s,%s,%s,%s,%s))', [
                    '9223372036854775806',
                    '-9223372036854775807',

                    '9223372036854775807',
                    '-9223372036854775808',

                    '9223372036854775808',
                    '-9223372036854775809',

                    '9223372036854775809',
                    '-9223372036854775810',
                ]),
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        (int)9223372036854775806,
                        (int)-9223372036854775807,
                        (int)9223372036854775807,
                        (int)-9223372036854775808,

                        (float)9223372036854775808,
                        (float)-9223372036854775809,
                        (float)9223372036854775809,
                        (float)-9223372036854775810,
                    ]))
                    ->getQuery(),
                false // these never fully match back when converting them again
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataSyntaxError()
    {
        return [
            'unexpected token' => [
                '1',
                sprintf('Unexpected token "%s" (%s) at position %d', '1', 'T_INTEGER', 0),
            ],

            'unknown typecaster' => [
                'eq(field,unknown:value)',
                sprintf('Unknown type "%s"', 'unknown'),
            ],

            'mix group operators 1' => [
                '(a=b|c=d&e=f)',
                sprintf('Cannot mix "%s" and "%s" within a group', '&', '|'),
            ],
            'mix group operators 2' => [
                '(a=b&c=d|e=f)',
                sprintf('Cannot mix "%s" and "%s" within a group', '&', '|'),
            ],

            'invalid scalar value 1 ' => [
                'eq(field,*glob?)',
                sprintf('Invalid scalar token "%s" (%s)', '*glob?', 'T_GLOB'),
            ],
            'invalid scalar value 2' => [
                'eq(field,(1,2))',
                sprintf('Invalid scalar token "%s" (%s)', '(', 'T_OPEN_PARENTHESIS'),
            ],

            'invalid array value 1 ' => [
                'in(field,1)',
                sprintf('Unexpected token "%s" (%s) (expected %s)', '1', 'T_INTEGER', 'T_OPEN_PARENTHESIS'),
            ],
            'invalid array value 2' => [
                'in(field,(1,(2)))',
                sprintf('Invalid scalar token "%s" (%s)', '(', 'T_OPEN_PARENTHESIS'),
            ],
            'invalid array value 3' => [
                'in(field,(1,*glob?))',
                sprintf('Invalid scalar token "%s" (%s)', '*glob?', 'T_GLOB'),
            ],

            'invalid "and" node' => [
                'and(a=b)',
                sprintf('"%s" operator expects at least %d parameters, %d given', 'and', 2, 1),
            ],
            'invalid "or" node' => [
                'or(a=b)',
                sprintf('"%s" operator expects at least %d parameters, %d given', 'or', 2, 1),
            ],
            'invalid "not" node' => [
                'not(a=b,c=d)',
                sprintf('"%s" operator expects %d parameter, %d given', 'not', 1, 2),
            ],

            'limit: no args' => [
                'limit()',
                sprintf('Unexpected token "%s" (%s)', ')', 'T_CLOSE_PARENTHESIS'),
            ],
            'limit: many args' => [
                'limit(1,2,3)',
                sprintf('Unexpected token "%s" (%s)', ',', 'T_COMMA'),
            ],
            'limit: string limit' => [
                'limit(limit)',
                sprintf('Unexpected token "%s" (%s)', 'limit', 'T_STRING'),
            ],
            'limit: string offset' => [
                'limit(1,offset)',
                sprintf('Unexpected token "%s" (%s)', 'offset', 'T_STRING'),
            ],
        ];
    }

    private function encodeString($value)
    {
        return strtr(rawurlencode($value), [
            '-' => '%2D',
            '_' => '%5F',
            '.' => '%2E',
            '~' => '%7E',
        ]);
    }
}
