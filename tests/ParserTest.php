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
        $parser = new Parser();
        $parser->addTokenParser(new TokenParser\SelectTokenParser());
        $parser->addTokenParser(new TokenParser\QueryTokenParser());
        $parser->addTokenParser(new TokenParser\SortTokenParser());
        $parser->addTokenParser(new TokenParser\LimitTokenParser());

        $this->assertEquals($expected, $parser->parse((new Lexer())->tokenize($rql)));
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

            'typecast' => [
                'eq(a,string:3)',
                (new Query())
                    ->addQuery(new Node\Query\ScalarQuery\EqNode('a', '3')),
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
        ];
    }
}
