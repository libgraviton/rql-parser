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
                    ['eq', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['&', Token::T_AMPERSAND],
                    ['eq', Token::T_STRING],
                    ['&', Token::T_AMPERSAND],
                    ['limit', Token::T_LIMIT_OPERATOR],
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
                    ['eq', Token::T_QUERY_OPERATOR],
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
                    ['in', Token::T_QUERY_OPERATOR],
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

                    ['out', Token::T_QUERY_OPERATOR],
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
                    ['eq', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['lt', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['d', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
            'logic operators' => [
                'and(eq(a,b),lt(c,d),or(in(a,(1,f)),gte(g,2)))&not(ne(h,3))',
                [
                    ['and', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['eq', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [',', Token::T_COMMA],

                    ['lt', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['c', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['d', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [',', Token::T_COMMA],

                    ['or', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],

                    ['in', Token::T_QUERY_OPERATOR],
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

                    ['gte', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['g', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['2', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    [')', Token::T_CLOSE_PARENTHESIS],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['not', Token::T_QUERY_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['ne', Token::T_QUERY_OPERATOR],
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
                    ['select', Token::T_SELECT_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['b', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['c', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['sort', Token::T_SORT_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['a', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['+b', Token::T_STRING],
                    [',', Token::T_COMMA],
                    ['-c', Token::T_STRING],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['limit', Token::T_LIMIT_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['1', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],

                    ['&', Token::T_AMPERSAND],

                    ['limit', Token::T_LIMIT_OPERATOR],
                    ['(', Token::T_OPEN_PARENTHESIS],
                    ['1', Token::T_INTEGER],
                    [',', Token::T_COMMA],
                    ['2', Token::T_INTEGER],
                    [')', Token::T_CLOSE_PARENTHESIS],
                ],
            ],
        ];
    }
}
