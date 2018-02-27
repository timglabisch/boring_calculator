<?php

namespace Tg\Boringcalc;

interface CalculateAbleFactoryInterface
{
    public function supports(CalculateAbleInterface $calculateAble, $context): bool;

    public function createEmptyCalculated(CalculateAbleInterface $calculateAble, $context): CalculatedCalculateableInterface;

}