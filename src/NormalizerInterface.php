<?php
namespace Mrix\Rql\Parser;

/**
 * RQL query normalizer
 */
interface NormalizerInterface
{
    /**
     * @param string $rql
     * @return string
     */
    public function normalize($rql);
}
