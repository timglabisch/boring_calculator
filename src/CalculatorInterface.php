<?php

namespace Tg\Boringcalc;

interface CalculatorInterface
{
    public function supports(CalculatedCalculateableInterface $calculateable, array $metaDatas): bool;

    public function calculate(CalculatedCalculateableInterface $calculateable, array $metaDatas): CalculatedCalculateableInterface;
}