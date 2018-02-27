<?php

namespace Tg\Boringcalc;


interface CalculatedCalculateableInterface extends CalculateAbleInterface
{
    public function copy(): CalculatedCalculateableInterface;

    public function freeze();

    public function addHint(CalculatorHint $calculatorHint);

    /** return CalculatorHint[] */
    public function getHints(): array;

    /** @param CalculatorHint[] */
    public function setHints(array $hints);
}