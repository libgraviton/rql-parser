<?php
namespace Mrix\Rql\ParserTests;

use Mrix\Rql\Parser\Lexer;
use Mrix\Rql\Parser\Parser;
use Mrix\Rql\Parser\Query;
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
                (new Query())
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('name', 'value')),
            ],
            'array operators' => [
                'in(a,(1,b))&out(c,(2,d))',
                (new Query())
                    ->addQuery(new Node\Query\ArrayQuery\InNode('a', [1, 'b']))
                    ->addQuery(new Node\Query\ArrayQuery\OutNode('c', [2, 'd'])),
            ],
            'multiple query operators' => [
                'eq(a,b)&lt(c,d)',
                (new Query())
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('a', 'b'))
                    ->addQuery(new Node\Query\ScalarQuery\LtNode('c', 'd')),
            ],
            'logic operators' => [
                'and(eq(a,b),lt(c,d),or(in(a,(1,f)),gte(g,2)))&not(ne(h,3))',
                (new Query())
                    ->addQuery(new Node\Query\LogicQuery\AndNode([
                        new Node\Query\ScalarQuery\EqNode('a', 'b'),
                        new Node\Query\ScalarQuery\LtNode('c', 'd'),
                        new Node\Query\LogicQuery\OrNode([
                            new Node\Query\ArrayQuery\InNode('a', [1, 'f']),
                            new Node\Query\ScalarQuery\GteNode('g', 2),
                        ])
                    ]))
                    ->addQuery(new Node\Query\LogicQuery\NotNode([
                        new Node\Query\ScalarQuery\NeNode('h', 3),
                    ])),
            ],

            'select, sort and limit operators' => [
                'select(a,b,c)&sort(a,+b,-c)&limit(1,2)',
                (new Query())
                    ->addSelect(new Node\SelectNode(['a', 'b', 'c']))
                    ->addSort(new Node\SortNode([
                        'a' => Node\SortNode::SORT_ASC,
                        'b' => Node\SortNode::SORT_ASC,
                        'c' => Node\SortNode::SORT_DESC,
                    ]))
                    ->addLimit(new Node\LimitNode(1, 2)),
            ],

            'string typecast' => [
                'eq(a,string:3)&in(b,(string:true(),string:false,string:null,string:empty()))&out(c,(string:-1,string:+.5e10))',
                (new Query())
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('a', '3'))
                    ->addQuery(new Node\Query\ArrayQuery\InNode('b', [
                        'true',
                        'false',
                        'null',
                        '',
                    ]))
                    ->addQuery(new Node\Query\ArrayQuery\OutNode('c', [
                        '-1',
                        '+.5e10'
                    ])),
            ],
            'integer typecast' => [
                'eq(a,integer:0)&eq(b,integer:1.5)&eq(c,integer:null)&eq(d,integer:true)&eq(e,integer:a)&eq(f,integer:empty())',
                (new Query())
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('a', 0))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('b', 1))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('c', 0))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('d', 1))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('e', 0))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('f', 0)),
            ],
            'float typecast' => [
                'eq(a,float:0)&eq(b,float:1.5)&eq(c,float:null)&eq(d,float:true)&eq(e,float:a)&eq(f,float:empty())',
                (new Query())
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('a', 0.))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('b', 1.5))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('c', 0.))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('d', 1.))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('e', 0.))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('f', 0.)),
            ],
            'boolean typecast' => [
                'eq(a,boolean:0)&eq(b,boolean:1.5)&eq(c,boolean:null)&eq(d,boolean:true())&eq(e,boolean:a)&eq(f,boolean:empty())',
                (new Query())
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('a', false))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('b', true))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('c', false))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('d', true))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('e', true))
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('f', false)),
            ],
            'constants' => [
                'in(a,(null,null(),true,true(),false,false(),empty()))',
                (new Query())
                    ->addQuery(new Node\Query\ArrayQuery\InNode('a', [
                        null,
                        null,
                        true,
                        true,
                        false,
                        false,
                        '',
                    ])),
            ],
            'simple groups' => [
                '(eq(a,b)&lt(c,d))&(ne(e,f)|gt(g,h))',
                (new Query())
                    ->addQuery(new Node\Query\LogicQuery\AndNode([
                        new Node\Query\ScalarQuery\EqNode('a', 'b'),
                        new Node\Query\ScalarQuery\LtNode('c', 'd'),
                    ]))
                    ->addQuery(new Node\Query\LogicQuery\OrNode([
                        new Node\Query\ScalarQuery\NeNode('e', 'f'),
                        new Node\Query\ScalarQuery\GtNode('g', 'h'),
                    ])),
            ],
            'deep groups & mix groups with operators' => [
                '(eq(a,b)|lt(c,d)|and(gt(e,f),(ne(g,h)|gte(i,j)|in(k,(l,m,n)))))',
                (new Query())
                    ->addQuery(new Node\Query\LogicQuery\OrNode([
                        new Node\Query\ScalarQuery\EqNode('a', 'b'),
                        new Node\Query\ScalarQuery\LtNode('c', 'd'),
                        new Node\Query\LogicQuery\AndNode([
                            new Node\Query\ScalarQuery\GtNode('e', 'f'),
                            new Node\Query\LogicQuery\OrNode([
                                new Node\Query\ScalarQuery\NeNode('g', 'h'),
                                new Node\Query\ScalarQuery\GteNode('i', 'j'),
                                new Node\Query\ArrayQuery\InNode('k', ['l', 'm', 'n']),
                            ]),
                        ]),
                    ])),
            ],
        ];
    }
}
