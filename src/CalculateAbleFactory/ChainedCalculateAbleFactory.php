<?php

namespace Tg\Boringcalc\CalculateAbleFactory;

use Tg\Boringcalc\CalculateAbleFactoryInterface;
use Tg\Boringcalc\CalculateAbleInterface;
use Tg\Boringcalc\CalculatedCalculateableInterface;

class ChainedCalculateAbleFactory implements CalculateAbleFactoryInterface
{
    /** @var CalculateAbleFactoryInterface[] */
    private $impls;

    public function __construct(array $impls)
    {
        $this->impls = $impls;
    }

    public function supports(CalculateAbleInterface $calculateAble, $context): bool
    {
        foreach ($this->impls as $impl) {
            if ($impl->supports($calculateAble, $context)) {
                return true;
            }
        }

        return false;
    }

    public function createEmptyCalculated(CalculateAbleInterface $calculateAble, $context): CalculatedCalculateableInterface
    {
        foreach ($this->impls as $impl) {
            if ($impl->supports($calculateAble, $context)) {
                return $impl->createEmptyCalculated($calculateAble, $context);
            }
        }

        throw new \LogicException("could not create fresh object from type ". get_class($calculateAble));
    }
}