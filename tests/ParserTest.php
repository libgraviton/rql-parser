<?php
namespace Xiag\Rql\ParserTests;

use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\QueryBuilder;
use Xiag\Rql\Parser\TokenParser;
use Xiag\Rql\Parser\Node;
use Xiag\Rql\Parser\DataType\DateTime;
use Xiag\Rql\Parser\DataType\Glob;

/**
 * @covers Parser
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $rql
     * @param Query $expected
     * @return void
     *
     * @covers Parser::parse()
     * @dataProvider dataParse()
     */
    public function testParse($rql, Query $expected)
    {
        $lexer = new Lexer();
        $parser = Parser::createDefault();

        $this->assertSame(
            var_export($expected, true),
            var_export($parser->parse($lexer->tokenize($rql)), true)
        );
    }

    /**
     * @param string $rql
     * @param string $exceptionMessage
     * @return void
     *
     * @covers Parser::parse()
     * @dataProvider dataSyntaxError()
     */
    public function testSyntaxError($rql, $exceptionMessage)
    {
        $this->setExpectedException('Xiag\Rql\Parser\Exception\SyntaxErrorException', $exceptionMessage);

        $lexer = new Lexer();
        $parser = Parser::createDefault();

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
                    ->addQuery(new Node\Query\LogicOperator\AndNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                        new Node\Query\LogicOperator\OrNode([
                            new Node\Query\ArrayOperator\InNode('a', [1, 'f']),
                            new Node\Query\ScalarOperator\GtNode('g', 2),
                        ])
                    ]))
                    ->addQuery(new Node\Query\LogicOperator\NotNode([
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
                'eq(a,string:3)&in(b,(string:true(),string:false,string:null,string:empty()))&out(c,(string:-1,string:+.5e10))',
                (new QueryBuilder())
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
                'eq(a,integer:0)&eq(b,integer:1.5)&eq(c,integer:null)&eq(d,integer:true)&eq(e,integer:a)&eq(f,integer:empty())',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('b', 1))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('c', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('d', 1))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('e', 0))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('f', 0))
                    ->getQuery(),
            ],
            'float typecast' => [
                'eq(a,float:0)&eq(b,float:1.5)&eq(c,float:null)&eq(d,float:true)&eq(e,float:a)&eq(f,float:empty())',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('b', 1.5))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('c', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('d', 1.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('e', 0.))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('f', 0.))
                    ->getQuery(),
            ],
            'boolean typecast' => [
                'eq(a,boolean:0)&eq(b,boolean:1.5)&eq(c,boolean:null)&eq(d,boolean:true())&eq(e,boolean:a)&eq(f,boolean:empty())',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('a', false))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('b', true))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('c', false))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('d', true))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('e', true))
                    ->addQuery(new Node\Query\ScalarOperator\EqNode('f', false))
                    ->getQuery(),
            ],
            'constants' => [
                'in(a,(null,null(),true,true(),false,false(),empty()))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        null,
                        null,
                        true,
                        true,
                        false,
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
            'simple groups' => [
                '(eq(a,b)&lt(c,d))&(ne(e,f)|gt(g,h))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\LogicOperator\AndNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                    ]))
                    ->addQuery(new Node\Query\LogicOperator\OrNode([
                        new Node\Query\ScalarOperator\NeNode('e', 'f'),
                        new Node\Query\ScalarOperator\GtNode('g', 'h'),
                    ]))
                    ->getQuery(),
            ],
            'deep groups & mix groups with operators' => [
                '(eq(a,b)|lt(c,d)|and(gt(e,f),(ne(g,h)|ge(i,j)|in(k,(l,m,n))|(o<>p&q=le=r))))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\LogicOperator\OrNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                        new Node\Query\LogicOperator\AndNode([
                            new Node\Query\ScalarOperator\GtNode('e', 'f'),
                            new Node\Query\LogicOperator\OrNode([
                                new Node\Query\ScalarOperator\NeNode('g', 'h'),
                                new Node\Query\ScalarOperator\GeNode('i', 'j'),
                                new Node\Query\ArrayOperator\InNode('k', ['l', 'm', 'n']),
                                new Node\Query\LogicOperator\AndNode([
                                    new Node\Query\ScalarOperator\NeNode('o', 'p'),
                                    new Node\Query\ScalarOperator\LeNode('q', 'r'),
                                ]),
                            ]),
                        ]),
                    ]))
                    ->getQuery(),
            ],
            'date support' => [
                'in(a,(2015-04-19,2012-02-29))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        DateTime::createFromRqlFormat('2015-04-19'),
                        DateTime::createFromRqlFormat('2012-02-29'),
                    ]))
                    ->getQuery(),
            ],
            'datetime support' => [
                'in(a,(2015-04-16T17:40:32Z,2012-02-29T17:40:32Z))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\ArrayOperator\InNode('a', [
                        DateTime::createFromRqlFormat('2015-04-16T17:40:32Z'),
                        DateTime::createFromRqlFormat('2012-02-29T17:40:32Z'),
                    ]))
                    ->getQuery(),
            ],
            'string encoding' => [
                vsprintf('in(a,(%s,%s,%s,%s,%s,%s,%s,%s,%s))', [
                    $this->encodeString('+a-b:c'),
                    'null()',
                    $this->encodeString('null()'),
                    '2015-04-19T21:00:00Z',
                    $this->encodeString('2015-04-19T21:00:00Z'),
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
                        DateTime::createFromRqlFormat('2015-04-19T21:00:00Z'),
                        '2015-04-19T21:00:00Z',
                        1.1e+3,
                        '1.1e+3',
                        new Glob('*abc?'),
                        '*abc?',
                    ]))
                    ->getQuery(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataSyntaxError()
    {
        return [
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
