<?php

namespace Tg\Boringcalc\Calculator;

use Tg\Boringcalc\CalculateAbleFactoryInterface;
use Tg\Boringcalc\CalculateAbleInterface;
use Tg\Boringcalc\CalculatorInterface;
use Tg\Boringcalc\MetaDataProviderInterface;

class BoringCalculator
{
    private static $seconds_before_recalculation = 5;

    protected $dateTimeProviderService;

    /** @var MetaDataProviderInterface */
    protected $metaDataProvider;

    /** @var CalculateAbleFactoryInterface */
    protected $calculateAbleFacory;

    /** @var CalculatorInterface[] */
    protected $calculators = [];


    public function __construct(
        MetaDataProviderInterface $metaDataProvider,
        CalculateAbleFactoryInterface $calculateAbleFactory,
        $calculators
    ) {
        $this->metaDataProvider = $metaDataProvider;
        $this->calculateAbleFacory = $calculateAbleFactory;
        array_map([$this, 'addCalculator'], $calculators);
    }

    /** @param CalculatorInterface $calculator */
    private function addCalculator(CalculatorInterface $calculator)
    {
        $this->calculators[] = $calculator;
    }

    public function calculate(CalculateAbleInterface $calculateAble, $context)
    {
        $calculatedUnfozen = $this->calculateAbleFacory->createEmptyCalculated($calculateAble, $context);

        // we can keep track of all modifications
        $evolution = [];

        $metaDatas = $this->metaDataProvider->getMetaData($calculatedUnfozen->copy(), $context);

        foreach ($this->calculators as $calculator) {

            if (!$x = $calculator->supports($calculatedUnfozen->copy(), $metaDatas)) {
                continue;
            }

            $evolution[] = clone $calculatedUnfozen->copy();
            $calculateAble = $calculator->calculate($calculatedUnfozen->copy(), $metaDatas);
        }

        $calculateAble->freeze();

        return $calculateAble;
    }
}