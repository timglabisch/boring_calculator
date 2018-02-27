<?php

namespace Tg\Boringcalc;

interface MetaDataProviderInterface
{
    /** @return MetaDataInterface[] */
    public function getMetaData(CalculateAbleInterface $calculateAble, $context): array;
}