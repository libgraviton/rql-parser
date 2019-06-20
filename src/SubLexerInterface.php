<?php
namespace Graviton\RqlParser;

interface SubLexerInterface
{
    /**
     * @param string $code
     * @param int $cursor
     * @return Token|null
     */
    public function getTokenAt($code, $cursor);
}
