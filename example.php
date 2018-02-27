<?php

use Tg\Boringcalc\CalculateAbleInterface;
use Tg\Boringcalc\CalculatedCalculateableInterface;

require __DIR__ . '/vendor/autoload.php';

class Document implements \Tg\Boringcalc\CalculatedCalculateableInterface {

    use \Tg\Boringcalc\HelperTraits\HintStorageTrait;

    public $discountAmount;

    public $discountPercent;

    /** @var DocumentPosition[] */
    public $positions;

    public function copy(): \Tg\Boringcalc\CalculatedCalculateableInterface
    {
        return clone $this;
    }

    public function freeze()
    {
        // TODO: Implement freeze() method.
    }

}

class DocumentPosition {

    public $price;

    public $discountPercent = 0;

    public $vat;

    public $calculatedPrice;

    public function __construct($price, $discountAmount, $vat)
    {
        $this->price = $price;
        $this->vat = $vat;
    }
}

class PositionSharedDiscountPercent implements \Tg\Boringcalc\CalculatorHint {

    /** @var DocumentPosition */
    public $position;

    /** @var int */
    public $discountPercent;

    /** @var int */
    public $positionPriceNet;

    public function __construct(DocumentPosition $position, int $discountPercent, int $positionPriceNet)
    {
        $this->position = $position;
        $this->discountPercent = $discountPercent;
        $this->positionPriceNet = $positionPriceNet;
    }

}

class PositionDiscountAmount implements \Tg\Boringcalc\CalculatorHint {

    /** @var DocumentPosition */
    public $position;

    /** @var int */
    public $discountPriceNet;

    public function __construct(DocumentPosition $position, int $discountPriceNet)
    {
        $this->position = $position;
        $this->discountPriceNet = $discountPriceNet;
    }

}

$document = new Document();
$document->discountAmount = 10;
$document->positions = [
    new DocumentPosition(10, 0, 10),
    new DocumentPosition(10, 0, 20)
];


abstract class DocumentCalculator implements \Tg\Boringcalc\CalculatorInterface {

    public function supports(CalculatedCalculateableInterface $calculateable, array $metaDatas): bool
    {
        return $calculateable instanceof Document;
    }

    public function calculate(CalculatedCalculateableInterface $calculateable, array $metaDatas): CalculatedCalculateableInterface
    {
        return $this->calculateDocument($calculateable, $metaDatas);
    }

    abstract function calculateDocument(Document $document, array $metaDatas): Document;
}


$calculators = [
    new class extends DocumentCalculator {

        public function calculateDocument(Document $document, array $metaDatas): Document
        {
            foreach ($document->positions as $position) {
                $position->calculatedPrice = $position->price; // basic price handling, gross, net etc.
            }

            return $document;
        }

    },
    new class extends DocumentCalculator {

        public function calculateDocument(Document $document, array $metaDatas): Document
        {
            foreach ($document->positions as $position) {
                $this->calculatePosition($document, $position);
            }

            return $document;
        }

        private function calculatePosition(Document $document, DocumentPosition $position) {

            // if we've a global discount, just add a hint
            if ($document->discountAmount) {
                $document->addHint(new PositionDiscountAmount($position, $position->price));
            }

            if ($position->discountPercent) {
                $document->addHint(new PositionSharedDiscountPercent($position, $document->discountAmount, $position->price));
            }
        }
    },
    // discounts auf positionsebene verrechnen
    new class extends DocumentCalculator {

        public function calculateDocument(Document $document, array $metaDatas): Document
        {
            foreach ($document->getHints() as $hint) {
                if (!$hint instanceof PositionDiscountAmount) {
                    continue;
                }

                foreach ($document->positions as $position) {
                    if ($hint->position !== $position) {
                        continue;
                    }

                    $position->price -= $hint->discountPriceNet;
                }
            }

            return $document;
        }
    },
    // globale discounts verrechnen
    new class extends DocumentCalculator {

        public function calculateDocument(Document $document, array $metaDatas): Document
        {
            $vatPositionMap = [];

            foreach ($document->positions as $position) {
                if (isset($vatPositionMap[$position->vat])) {
                    $vatPositionMap[$position->vat] = [];
                }

                $vatPositionMap[$position->vat][] = $position;
            }

            foreach ($vatPositionMap as $vat => $positions) {
                // summenerhaltendes runden auf $positions und jede position updaten .....
            }

            return $document;
        }
    }
];

$calculator = new \Tg\Boringcalc\Calculator\BoringCalculator(
    new \Tg\Boringcalc\MetaDataProvider\ChainedMetaDataProvider([]),
    new Tg\Boringcalc\CalculateAbleFactory\ChainedCalculateAbleFactory([
        new class implements \Tg\Boringcalc\CalculateAbleFactoryInterface {
            public function supports(CalculateAbleInterface $calculateAble, $context): bool
            {
                return $calculateAble instanceof Document;
            }

            /**
             * @param Document $calculateAble
             * @param $context
             * @return CalculatedCalculateableInterface
             */
            public function createEmptyCalculated(CalculateAbleInterface $calculateAble, $context): CalculatedCalculateableInterface
            {
                foreach ($calculateAble->positions as $position) {
                    $position->calculatedPrice = 0;
                }

                return $calculateAble;
            }
        }
    ]),
    $calculators
);

/** @var Document $calculatedDocument */
$calculatedDocument = $calculator->calculate($document, null);

foreach ($calculatedDocument->positions as $position) {
    echo "{$position->calculatedPrice}";
}


var_dump();
