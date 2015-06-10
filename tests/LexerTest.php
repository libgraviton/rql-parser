<?php
namespace Xiag\Rql\ParserTests;

use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Token;

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

        $this->assertSame(count($stream), count($expected) + 1);

        foreach ($expected as $token) {
            list($value, $type) = $token;

            $this->assertSame(
                $value,
                $stream->getCurrent()->getValue(),
                sprintf('"%s" != "%s"', $value, $stream->getCurrent()->getValue())
            );
            $this->assertSame(
                $type,
                $stream->getCurrent()->getType(),
                sprintf('"%s" != "%s"', Token::getTypeName($type), Token::getTypeName($stream->getCurrent()->getType()))
            );

            $stream->next();
        }
    }

    /**
     * @param string $rql
     * @param string $exceptionMessage
     * @return void
     *
     * @covers Lexer::tokenize()
     * @dataProvider dataSyntaxError()
     */
    public function testSyntaxError($rql, $exceptionMessage)
    {
        $this->setExpectedException('Xiag\Rql\Parser\Exception\SyntaxErrorException', $exceptionMessage);

        $lexer = new Lexer();
        $lexer->tokenize($rql);
    }

    /**
     * @return array
     */
    public function dataTokenize()
    {
        return [
            'primitives' => [
                'eq(&eq&limit(limit,)date:empty(),null,1,+1,-1,0,1.5,-.4e12,2015-04-19,2015-04-16T17:40:32Z,*abc?',
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
                    [',', Token::T_COMMA],
                    ['2015-04-19', Token::T_DATE],
                    [',', Token::T_COMMA],
                    ['2015-04-16T17:40:32Z', Token::T_DATE],
                    [',', Token::T_COMMA],
                    ['*abc?', Token::T_GLOB],
                ],
            ],
            'string encoding' => [
                vsprintf('in(a,(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s))', [
                    '+abc',
                    $this->encodeString('+abc'),
                    '-abc',
                    $this->encodeString('-abc'),
                    'null()',
                    $this->encodeString('null()'),
                    '2015-04-19T21:00:00Z',
                    $this->encodeString('2015-04-19T21:00:00Z'),
                    '1.1e+3',
                    $this->encodeString('1.1e+3'),
                    '*abc?',
                    $this->encodeString('*abc?'),
                ]),
                [
                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['+', Token::T_PLUS],
                    ['abc', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['+abc', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['-', Token::T_MINUS],
                    ['abc', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['-abc', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['null()', Token::T_NULL],
                    [',', Token::T_COMMA],
                    ['null()', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['2015-04-19T21:00:00Z', Token::T_DATE],
                    [',', Token::T_COMMA],
                    ['2015-04-19T21:00:00Z', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['1.1e+3', Token::T_FLOAT],
                    [',', Token::T_COMMA],
                    ['1.1e+3', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['*abc?', Token::T_GLOB],
                    [',', Token::T_COMMA],
                    ['*abc?', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],

            'date support' => [
                'in(a,(2015-04-19,2012-02-29))',
                [
                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['2015-04-19', Token::T_DATE],
                    [',', Token::T_COMMA],
                    ['2012-02-29', Token::T_DATE],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'datetime support' => [
                'in(a,(2015-04-16T17:40:32Z,2012-02-29T17:40:32Z))',
                [
                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['2015-04-16T17:40:32Z', Token::T_DATE],
                    [',', Token::T_COMMA],
                    ['2012-02-29T17:40:32Z', Token::T_DATE],
                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],
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
            'scalar operators' => [
                'eq(a,1)&ne(b,2)&lt(c,3)&gt(d,4)&le(e,5)&ge(f,6)&like(g,*abc?)',
                [
                    ['eq', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['1', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['ne', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['b', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['2', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['lt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['3', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['gt', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['d', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['4', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['le', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['e', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['5', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['ge', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['f', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['6', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['like', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['g', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['*abc?', Token::T_GLOB],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'array operators' => [
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
                'select(a,b,c)&sort(+a,-b)&limit(1)&limit(1,2)',
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
                    ['+', Token::T_PLUS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['-', Token::T_MINUS],
                    ['b', Token::T_STRING],
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
            'fiql operators' => [
                'a=eq=1&b=ne=2&c=lt=3&d=gt=4&e=le=5&f=ge=6&g=in=(7,8)&h=out=(9,10)&i=like=*abc?',
                [
                    ['a', Token::T_STRING],
                    ['eq', Token::T_OPERATOR],
                    ['1', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['b', Token::T_STRING],
                    ['ne', Token::T_OPERATOR],
                    ['2', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['c', Token::T_STRING],
                    ['lt', Token::T_OPERATOR],
                    ['3', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['d', Token::T_STRING],
                    ['gt', Token::T_OPERATOR],
                    ['4', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['e', Token::T_STRING],
                    ['le', Token::T_OPERATOR],
                    ['5', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['f', Token::T_STRING],
                    ['ge', Token::T_OPERATOR],
                    ['6', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['g', Token::T_STRING],
                    ['in', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['7', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['8', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['h', Token::T_STRING],
                    ['out', Token::T_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['9', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['10', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['i', Token::T_STRING],
                    ['like', Token::T_OPERATOR],
                    ['*abc?', Token::T_GLOB],
                ],
            ],
            'fiql operators (json compatible)' => [
                'a=1&b==2&c<>3&d!=4&e<5&f>6&g<=7&h>=8',
                [
                    ['a', Token::T_STRING],
                    ['=', Token::T_OPERATOR],
                    ['1', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['b', Token::T_STRING],
                    ['==', Token::T_OPERATOR],
                    ['2', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['c', Token::T_STRING],
                    ['<>', Token::T_OPERATOR],
                    ['3', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['d', Token::T_STRING],
                    ['!=', Token::T_OPERATOR],
                    ['4', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['e', Token::T_STRING],
                    ['<', Token::T_OPERATOR],
                    ['5', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['f', Token::T_STRING],
                    ['>', Token::T_OPERATOR],
                    ['6', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['g', Token::T_STRING],
                    ['<=', Token::T_OPERATOR],
                    ['7', Token::T_INTEGER],

                    ['&', Token::T_AMPERSAND],

                    ['h', Token::T_STRING],
                    ['>=', Token::T_OPERATOR],
                    ['8', Token::T_INTEGER],
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
                '(eq(a,b)|lt(c,d)|and(gt(e,f),(ne(g,h)|gt(i,j)|in(k,(l,m,n))|(o<>p&q=le=r))))',
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

                    ['|', Token::T_VERTICAL_BAR],

                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['o', Token::T_STRING],
                    ['<>', Token::T_OPERATOR],
                    ['p', Token::T_STRING],
                    ['&', Token::T_AMPERSAND],
                    ['q', Token::T_STRING],
                    ['le', Token::T_OPERATOR],
                    ['r', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataSyntaxError()
    {
        return [
            'invalid date 1' => [
                'in(a,(2012-02-29,2015-02-29))',
                sprintf('Invalid date value "%s"', '2015-02-29'),
            ],
            'invalid date 2' => [
                'in(a,(2015-12-19,2015-13-19))',
                sprintf('Invalid date value "%s"', '2015-13-19'),
            ],

            'invalid datetime 1' => [
                'in(a,(2015-04-16T17:40:32Z,2015-02-30T17:40:32Z))',
                sprintf('Invalid datetime value "%s"', '2015-02-30T17:40:32Z'),
            ],
            'invalid datetime 2' => [
                'in(a,(2015-12-19T17:40:32Z,2015-13-19T17:40:32Z))',
                sprintf('Invalid datetime value "%s"', '2015-13-19T17:40:32Z'),
            ],

            'invalid string 1' => [
                'eq(a,+a+b)',
                sprintf('String value "%s" contains unencoded character "%s"', 'a+b', '+'),
            ],
            'invalid string 2' => [
                'eq(a,-a-b)',
                sprintf('String value "%s" contains unencoded character "%s"', 'a-b', '-'),
            ],
            'invalid string 3' => [
                'eq(a,2:b)',
                sprintf('String value "%s" contains unencoded character "%s"', '2:b', ':'),
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
