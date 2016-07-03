<?php
namespace Xiag\Rql\Parser;

interface SubLexerInterface
{
    /**
     * @param string $code
     * @param int $cursor
     * @return Token|null
     */
    public function getTokenAt($code, $cursor);
}
