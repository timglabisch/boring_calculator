<?php

namespace Tg\Boringcalc\MetaDataProvider;

use Tg\Boringcalc\CalculateAbleInterface;
use Tg\Boringcalc\MetaDataInterface;
use Tg\Boringcalc\MetaDataProviderInterface;

class ChainedMetaDataProvider implements MetaDataProviderInterface
{
    /** @var MetaDataInterface[] */
    private $impls;

    public function __construct(array $impls)
    {
        $this->impls = $impls;
    }

    /** @return MetaDataInterface[] */
    public function getMetaData(CalculateAbleInterface $calculateAble, $context): array
    {
        $buffer = [];
        foreach ($this->impls as $impl) {
            foreach ($impl->getMetaData($calculateAble, $context) as $metaData) {
                $buffer[] = $metaData;
            }
        }

        return $buffer;
    }

}