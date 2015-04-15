<?php
namespace Mrix\Rql\ParserTests;

use Mrix\Rql\Parser\Lexer;
use Mrix\Rql\Parser\Token;

/**
 * @covers Lexer
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $rql
     * @param array $expected
     * @return void
     *
     * @covers Lexer::tokenize()
     * @dataProvider dataTokenize()
     */
    public function testTokenize($rql, $expected)
    {
        $lexer = new Lexer();
        $stream = $lexer->tokenize($rql);

        foreach ($expected as list($value, $type)) {
            $this->assertSame($value, $stream->getCurrent()->getValue());
            $this->assertSame($type, $stream->getCurrent()->getType());

            $stream->next();
        }
    }

    /**
     * @return array
     */
    public function dataTokenize()
    {
        return [
            'primitives' => [
                'eq(&eq&limit(limit,)date:empty(),null,1,+1,-1,0,1.5,-.4e12',
                [
                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['&', Token::T_AMPERSAND],
                    ['eq', Token::T_STRING],
                    ['&', Token::T_AMPERSAND],
                    ['limit', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['limit', Token::T_STRING],
                    [',', Token::T_COMMA],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    ['date', Token::T_TYPE],
                    ['empty()', Token::T_EMPTY],
                    [',', Token::T_COMMA],
                    ['null', Token::T_NULL],
                    [',', Token::T_COMMA],
                    ['1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['+1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['-1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['0', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['1.5', Token::T_FLOAT],
                    [',', Token::T_COMMA],
                    ['-.4e12', Token::T_FLOAT],
                ],
            ],

            'simple eq' => [
                'eq(name,value)',
                [
                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['name', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['value', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'array oprators' => [
                'in(a,(1,b))&out(c,(2,d))',
                [
                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['out', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['2', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['d', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'multiple query operators' => [
                'eq(a,b)&lt(c,d)',
                [
                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['lt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['d', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'logic operators' => [
                'and(eq(a,b),lt(c,d),or(in(a,(1,f)),gt(g,2)))&not(ne(h,3))',
                [
                    ['and', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [',', Token::T_COMMA],

                    ['lt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['d', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [',', Token::T_COMMA],

                    ['or', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['f', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [',', Token::T_COMMA],

                    ['gt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['g', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['2', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['not', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['ne', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['h', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['3', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],

            'select, sort and limit operators' => [
                'select(a,b,c)&sort(a,+b,-c)&limit(1)&limit(1,2)',
                [
                    ['select', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['c', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['sort', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['+b', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['-c', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['limit', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['1', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['limit', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['2', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],

            'string typecast' => [
                'eq(a,string:3)&in(b,(string:true(),string:false,string:null,string:empty()))&out(c,(string:-1,string:+.5e10))',
                [
                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['string', Token::T_TYPE],
                    ['3', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['b', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['string', Token::T_TYPE],
                    ['true()', Token::T_TRUE],
                    [',', Token::T_COMMA],
                    ['string', Token::T_TYPE],
                    ['false', Token::T_FALSE],
                    [',', Token::T_COMMA],
                    ['string', Token::T_TYPE],
                    ['null', Token::T_NULL],
                    [',', Token::T_COMMA],
                    ['string', Token::T_TYPE],
                    ['empty()', Token::T_EMPTY],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['out', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['string', Token::T_TYPE],
                    ['-1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['string', Token::T_TYPE],
                    ['+.5e10', Token::T_FLOAT],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'constants' => [
                'in(a,(null,null(),true,true(),false,false(),empty()))',
                [
                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['null', Token::T_NULL],
                    [',', Token::T_COMMA],
                    ['null()', Token::T_NULL],
                    [',', Token::T_COMMA],
                    ['true', Token::T_TRUE],
                    [',', Token::T_COMMA],
                    ['true()', Token::T_TRUE],
                    [',', Token::T_COMMA],
                    ['false', Token::T_FALSE],
                    [',', Token::T_COMMA],
                    ['false()', Token::T_FALSE],
                    [',', Token::T_COMMA],
                    ['empty()', Token::T_EMPTY],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'simple groups' => [
                '(eq(a,b)&lt(c,d))&(ne(e,f)|gt(g,h))',
                [
                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['lt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['d', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['ne', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['e', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['f', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['|', Token::T_VERTICAL_BAR],

                    ['gt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['g', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['h', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'deep groups & mix groups with operators' => [
                '(eq(a,b)|lt(c,d)|and(gt(e,f),(ne(g,h)|gt(i,j)|in(k,(l,m,n)))))',
                [
                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['|', Token::T_VERTICAL_BAR],

                    ['lt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['d', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['|', Token::T_VERTICAL_BAR],

                    ['and', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['gt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['e', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['f', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [',', Token::T_COMMA],

                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['ne', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['g', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['h', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['|', Token::T_VERTICAL_BAR],

                    ['gt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['i', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['j', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['|', Token::T_VERTICAL_BAR],

                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['k', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['l', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['m', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['n', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
        ];
    }
}
