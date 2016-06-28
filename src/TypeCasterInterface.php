<?php
namespace Xiag\Rql\Parser;

interface TypeCasterInterface
{
    /**
     * @param Token $token
     * @return mixed
     */
    public function typeCast(Token $token);
}
