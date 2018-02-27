<?php

namespace Tg\Boringcalc\HelperTraits;

trait HintStorageTrait
{
    protected $hints;

    public function addHint(\Tg\Boringcalc\CalculatorHint $calculatorHint)
    {
        $this->hints[] = $calculatorHint;
    }

    public function getHints(): array
    {
        return $this->hints;
    }

    public function setHints(array $hints)
    {
        $this->hints = $hints;
    }
}