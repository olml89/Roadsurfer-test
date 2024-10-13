<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Shared\Domain\DataProvider\DataProvider;
use App\Shared\Domain\DataProvider\UnreachableDataException;
use UnexpectedValueException;

final readonly class Importer
{
    public function __construct(
        private DataProvider $dataProvider,
        private EdibleFactory $edibleFactory,
    ) {}

    /**
     * @throws UnreachableDataException
     * @throws UnexpectedValueException
     */
    public function import(string $file): int
    {
        $fruitCollection = new FruitCollection();
        $vegetableCollection = new VegetableCollection();

        foreach ($this->dataProvider->getData($file) as $edibleData) {
            $edible = $this->edibleFactory->create($edibleData);

            match (true) {
                $edible instanceOf Fruit => $fruitCollection->add($edible),
                $edible instanceOf Vegetable => $vegetableCollection->add($edible),
                default => throw new UnexpectedValueException(sprintf(
                    'Collection for %s not implemented',
                    $edible::class,
                )),
            };
        }

        return $fruitCollection->count() + $vegetableCollection->count();
    }
}