<?php
namespace Graviton\RqlParserTests;

use PHPUnit\Framework\TestCase;
use Graviton\RqlParser\Glob;
use Graviton\RqlParser\Lexer;
use Graviton\RqlParser\Parser;
use Graviton\RqlParser\Node\Query\ScalarOperator\LikeNode;

class GlobTest extends TestCase
{
    /**
     * @param string $glob
     * @param string $expected
     * @return void
     *
     * @dataProvider dataToRql()
     */
    public function testToRql($glob, $expected)
    {
        $this->assertSame($expected, $this->getGlob($glob)->toRql());
    }

    /**
     * @param string $glob
     * @param string $expected
     * @return void
     *
     * @dataProvider dataToRegExp()
     */
    public function testToRegExp($glob, $expected)
    {
        $this->assertSame('^' . $expected . '$', $this->getGlob($glob)->toRegex());
    }

    /**
     * @param string $glob
     * @param string $expected
     * @return void
     *
     * @dataProvider dataToLike()
     */
    public function testToLike($glob, $expected)
    {
        $this->assertSame($expected, $this->getGlob($glob)->toLike());
    }

    /**
     * @param string $glob
     * @param string $expected
     * @return void
     *
     * @dataProvider dataToString()
     */
    public function testToString($glob, $expected)
    {
        $this->assertSame($expected, (string) $this->getGlob($glob));
    }

    /**
     * @return array
     * @see testToRql()
     */
    public function dataToRql()
    {
        return [
            'simple' => [
                'string',
                'string',
            ],
            'any' => [
                '*',
                '*',
            ],
            'one' => [
                '?',
                '?',
            ],
            'string' => [
                $this->escapeRql('*? abc ?*'),
                $this->escapeRql('*? abc ?*'),
            ],
            'number' => [
                '+1.5',
                $this->escapeRql('+1.5'),
            ],
            'date' => [
                '2016-06-30T23:33:55Z',
                $this->escapeRql('2016-06-30T23:33:55+0000'),
            ],
            'complex' => [
                '*' .
                $this->escapeRql('\\\\\\ 1 \* 2 \?') .
                'a*?b' .
                $this->escapeRql('\ 4 ? 5 * 6') .
                '*cd?' .
                $this->escapeRql('_ 7 " 8 \' 9') .
                '?',

                '*' .
                $this->escapeRql('\\\\\\ 1 \* 2 \?') .
                'a*?b' .
                $this->escapeRql('\ 4 ? 5 * 6') .
                '*cd?' .
                $this->escapeRql('_ 7 " 8 \' 9') .
                '?',
            ],
        ];
    }

    /**
     * @return array
     * @see testToRegExp()
     */
    public function dataToRegExp()
    {
        return [
            'simple' => [
                'string',
                'string',
            ],
            'any' => [
                '*',
                '.*',
            ],
            'one' => [
                '?',
                '.',
            ],
            'string' => [
                $this->escapeRql('*? abc ?*'),
                $this->escapeRegExp('*? abc ?*'),
            ],
            'number' => [
                '+1.5',
                $this->escapeRegExp('+1.5'),
            ],
            'date' => [
                '2016-06-30T23:33:55Z',
                $this->escapeRegExp('2016-06-30T23:33:55+0000'),
            ],
            'complex' => [
                '*' .
                $this->escapeRql('\\\\\\ 1 \* 2 \?') .
                'a*?b' .
                $this->escapeRql('\ 4 ? 5 * 6') .
                '*cd?' .
                $this->escapeRql('_ 7 " 8 \' 9') .
                '?',

                '.*' .
                $this->escapeRegExp('\\\\\\ 1 \* 2 \?') .
                'a.*.b' .
                $this->escapeRegExp('\ 4 ? 5 * 6') .
                '.*cd.' .
                $this->escapeRegExp('_ 7 " 8 \' 9') .
                '.',
            ],
        ];
    }

    /**
     * @return array
     * @see testToLike()
     */
    public function dataToLike()
    {
        return [
            'simple' => [
                'string',
                'string',
            ],
            'any' => [
                '*',
                '%',
            ],
            'one' => [
                '?',
                '_',
            ],
            'string' => [
                $this->escapeRql('*? abc ?*'),
                $this->escapeLike('*? abc ?*'),
            ],
            'number' => [
                '+1.5',
                $this->escapeLike('+1.5'),
            ],
            'date' => [
                '2016-06-30T23:33:55Z',
                $this->escapeLike('2016-06-30T23:33:55+0000'),
            ],
            'complex' => [
                '*' .
                $this->escapeRql('\\\\\\ 1 \* 2 \?') .
                'a*?b' .
                $this->escapeRql('\ 4 ? 5 * 6') .
                '*cd?' .
                $this->escapeRql('_ 7 " 8 \' 9') .
                '?',

                '%' .
                $this->escapeLike('\\\\\\ 1 \* 2 \?') .
                'a%_b' .
                $this->escapeLike('\ 4 ? 5 * 6') .
                '%cd_' .
                $this->escapeLike('_ 7 " 8 \' 9') .
                '_',
            ],
        ];
    }

    /**
     * @return array
     * @see testToString()
     */
    public function dataToString()
    {
        return [
            'simple' => [
                'string',
                'string',
            ],
            'any' => [
                '*',
                '*',
            ],
            'one' => [
                '?',
                '?',
            ],
            'string' => [
                $this->escapeRql('*? abc ?*'),
                $this->escapeGlob('*? abc ?*'),
            ],
            'number' => [
                '+1.5',
                $this->escapeGlob('+1.5'),
            ],
            'date' => [
                '2016-06-30T23:33:55Z',
                $this->escapeGlob('2016-06-30T23:33:55+0000'),
            ],
            'complex' => [
                '*' .
                $this->escapeRql('\\\\\\ 1 \* 2 \?') .
                'a*?b' .
                $this->escapeRql('\ 4 ? 5 * 6') .
                '*cd?' .
                $this->escapeRql('_ 7 " 8 \' 9') .
                '?',

                '*' .
                $this->escapeGlob('\\\\\\ 1 \* 2 \?') .
                'a*?b' .
                $this->escapeGlob('\ 4 ? 5 * 6') .
                '*cd?' .
                $this->escapeGlob('_ 7 " 8 \' 9') .
                '?',
            ],
        ];
    }

    /**
     * @param string $value
     * @return Glob
     */
    private function getGlob($value)
    {
        $lexer = new Lexer();
        $parser = new Parser();

        $rql = 'like(field,' . $value . ')';
        $stream = $lexer->tokenize($rql);
        $query = $parser->parse($stream);

        /** @var LikeNode $like */
        $like = $query->getQuery();
        $this->assertInstanceOf(LikeNode::class, $like);

        /** @var Glob $glob */
        $glob = $like->getValue();
        $this->assertInstanceOf(Glob::class, $glob);

        return $glob;
    }

    private function escapeRql($value)
    {
        return strtr(rawurlencode($value), [
            '-' => '%2D',
            '_' => '%5F',
            '.' => '%2E',
            '~' => '%7E',
        ]);
    }

    private function escapeRegExp($value)
    {
        return preg_quote($value, '/');
    }

    private function escapeLike($value)
    {
        return strtr($value, ['\\' => '\\\\', '_' => '\_', '%' => '\%']);
    }

    private function escapeGlob($value)
    {
        return strtr($value, ['\\' => '\\\\', '?' => '\?', '*' => '\*']);
    }
}
