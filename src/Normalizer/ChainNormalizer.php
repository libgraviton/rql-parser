<?php
namespace Mrix\Rql\Parser\Normalizer;

use Mrix\Rql\Parser\NormalizerInterface;

/**
 */
class ChainNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface[]
     */
    protected $normalizers = [];

    /**
     * @inheritdoc
     */
    public function normalize($rql)
    {
        foreach ($this->normalizers as $normalizer) {
            $rql = $normalizer->normalize($rql);
        }

        return $rql;
    }

    /**
     * @param NormalizerInterface $normalizer
     * @return $this
     */
    public function addNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizers[] = $normalizer;

        return $this;
    }
}
