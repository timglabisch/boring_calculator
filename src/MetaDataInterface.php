<?php

namespace Tg\Boringcalc;

interface MetaDataInterface
{
    /** @return MetaDataInterface[] */
    public function getMetaData(CalculateAbleInterface $calculateAble, $context): array;
}