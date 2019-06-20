<?php
namespace Graviton\RqlParser;

interface TypeCasterInterface
{
    /**
     * @param Token $token
     * @return mixed
     */
    public function typeCast(Token $token);
}
