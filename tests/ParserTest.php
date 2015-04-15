<?php
namespace Mrix\Rql\ParserTests;

use Mrix\Rql\Parser\Lexer;
use Mrix\Rql\Parser\Parser;
use Mrix\Rql\Parser\Query;
use Mrix\Rql\Parser\QueryBuilder;
use Mrix\Rql\Parser\TokenParser;
use Mrix\Rql\Parser\Node;

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

        $this->assertEquals($expected, $parser->parse($lexer->tokenize($rql)));
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
                'and(eq(a,b),lt(c,d),or(in(a,(1,f)),gte(g,2)))&not(ne(h,3))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\LogicOperator\AndNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                        new Node\Query\LogicOperator\OrNode([
                            new Node\Query\ArrayOperator\InNode('a', [1, 'f']),
                            new Node\Query\ScalarOperator\GteNode('g', 2),
                        ])
                    ]))
                    ->addQuery(new Node\Query\LogicOperator\NotNode([
                        new Node\Query\ScalarOperator\NeNode('h', 3),
                    ]))
                    ->getQuery(),
            ],

            'select, sort and limit operators' => [
                'select(a,b,c)&sort(a,+b,-c)&limit(1,2)',
                (new QueryBuilder())
                    ->addSelect(new Node\SelectNode(['a', 'b', 'c']))
                    ->addSort(new Node\SortNode([
                        'a' => Node\SortNode::SORT_ASC,
                        'b' => Node\SortNode::SORT_ASC,
                        'c' => Node\SortNode::SORT_DESC,
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
                '(eq(a,b)|lt(c,d)|and(gt(e,f),(ne(g,h)|gte(i,j)|in(k,(l,m,n)))))',
                (new QueryBuilder())
                    ->addQuery(new Node\Query\LogicOperator\OrNode([
                        new Node\Query\ScalarOperator\EqNode('a', 'b'),
                        new Node\Query\ScalarOperator\LtNode('c', 'd'),
                        new Node\Query\LogicOperator\AndNode([
                            new Node\Query\ScalarOperator\GtNode('e', 'f'),
                            new Node\Query\LogicOperator\OrNode([
                                new Node\Query\ScalarOperator\NeNode('g', 'h'),
                                new Node\Query\ScalarOperator\GteNode('i', 'j'),
                                new Node\Query\ArrayOperator\InNode('k', ['l', 'm', 'n']),
                            ]),
                        ]),
                    ]))
                    ->getQuery(),
            ],
        ];
    }
}
