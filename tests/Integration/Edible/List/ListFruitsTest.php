<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\List;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Fruit\FruitRepository;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use App\Shared\Domain\Criteria\CompositeExpression\Type;
use App\Shared\Domain\Criteria\Filter\Operator;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ListFruitsTest extends KernelTestCase
{
    use TestsEdibleListingEndpoint;

    protected function getEndpoint(): string
    {
        return '/fruits';
    }

    /**
     * @return array<string, empty|array{0: array<string, mixed>}|array{0: array<string, mixed>, 1: FruitCollection}>>
     */
    public static function provideExpectedFruits(): array
    {
        return [
            'no filters' => [

            ],
            'name filter' => [
                [
                    'name' => 'B',
                ],
                new FruitCollection(
                    new Fruit(
                        id: 8,
                        name: 'Berries',
                        quantity: Quantity::create(amount: 10000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 14,
                        name: 'Bananas',
                        quantity: Quantity::create(amount: 100000, unit: Unit::g),
                    ),
                ),
            ],
            // Not passing a valid amount will make quantity filter not being taken into consideration
            'quantity filter without amount' => [
                [
                    'quantity' => [
                        'op' => Operator::LTE->value,
                        'unit' => Unit::kg->value,
                    ],
                ],
            ],
            // This assumes a comparison for the exact value (EQ operator by default)
            'quantity filter without operator' => [
                [
                    'quantity' => [
                        'amount' => 24000,
                    ],
                ],
                new FruitCollection(
                    new Fruit(
                        id: 15,
                        name: 'Oranges',
                        quantity: Quantity::create(amount: 24000, unit: Unit::g),
                    ),
                ),
            ],
            'quantity filter with operator' => [
                [
                    'quantity' => [
                        'amount' => 24000,
                        'op' => Operator::GTE->value,
                    ],
                ],
                new FruitCollection(
                    new Fruit(
                        id: 4,
                        name: 'Melons',
                        quantity: Quantity::create(amount: 120000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 14,
                        name: 'Bananas',
                        quantity: Quantity::create(amount: 100000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 15,
                        name: 'Oranges',
                        quantity: Quantity::create(amount: 24000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 19,
                        name: 'Kumquat',
                        quantity: Quantity::create(amount: 90000, unit: Unit::g),
                    ),
                ),
            ],
            'quantity filter with unit' => [
                [
                    'quantity' => [
                        'amount' => 10,
                        'op' => Operator::LTE->value,
                        'unit' => Unit::kg->value,
                    ],
                ],
                new FruitCollection(
                    new Fruit(
                        id: 3,
                        name: 'Pears',
                        quantity: Quantity::create(amount: 3500, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 8,
                        name: 'Berries',
                        quantity: Quantity::create(amount: 10000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 16,
                        name: 'Avocado',
                        quantity: Quantity::create(amount: 10000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 18,
                        name: 'Kiwi',
                        quantity: Quantity::create(amount: 10000, unit: Unit::g),
                    ),
                ),
            ],
            'name and quantity filters' => [
                [
                    'name' => 'B',
                    'quantity' => [
                        'amount' => 50,
                        'op' => Operator::GTE->value,
                        'unit' => Unit::kg->value,
                    ],
                ],
                new FruitCollection(
                    new Fruit(
                        id: 14,
                        name: 'Bananas',
                        quantity: Quantity::create(amount: 100000, unit: Unit::g),
                    ),
                ),
            ],
            'quantity amount with a composite value' => [
                [
                    'quantity' => [
                        'amount' => '100,120',
                        'op' => Operator::IN->value,
                        'unit' => Unit::kg->value,
                    ],
                ],
                new FruitCollection(
                    new Fruit(
                        id: 4,
                        name: 'Melons',
                        quantity: Quantity::create(amount: 120000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 14,
                        name: 'Bananas',
                        quantity: Quantity::create(amount: 100000, unit: Unit::g),
                    ),
                ),
            ],
            'name and quantity filters joined by an OR operator' => [
                [
                    'name' => 'B',
                    'op' => Type::OR->value,
                    'quantity' => [
                        'amount' => 3.5,
                        'unit' => Unit::kg->value,
                    ],
                ],
                new FruitCollection(
                    new Fruit(
                        id: 3,
                        name: 'Pears',
                        quantity: Quantity::create(amount: 3500, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 8,
                        name: 'Berries',
                        quantity: Quantity::create(amount: 10000, unit: Unit::g),
                    ),
                    new Fruit(
                        id: 14,
                        name: 'Bananas',
                        quantity: Quantity::create(amount: 100000, unit: Unit::g),
                    ),
                ),
            ],
            'Pears converted to gr' => [
                [
                    'name' => 'Pears',
                    'unit' => Unit::g->value,
                ],
                new FruitCollection(
                    new Fruit(
                        id: 3,
                        name: 'Pears',
                        quantity: Quantity::create(amount: 3500, unit: Unit::g),
                    ),
                ),
            ],
            'Pears converted to kg' => [
                [
                    'name' => 'Pears',
                    'unit' => Unit::kg->value,
                ],
                new FruitCollection(
                    new Fruit(
                        id: 3,
                        name: 'Pears',
                        quantity: Quantity::create(amount: 3500, unit: Unit::g)->convertTo(Unit::kg),
                    ),
                ),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $queryString
     */
    #[DataProvider('provideExpectedFruits')]
    public function testItListsFruits(array $queryString = [], ?FruitCollection $expectedFruits = null): void
    {
        $fruits = new FruitCollection(
            new Fruit(
                id: 2,
                name: 'Apples',
                quantity: Quantity::create(amount: 20000, unit: Unit::g),
            ),
            new Fruit(
                id: 3,
                name: 'Pears',
                quantity: Quantity::create(amount: 3500, unit: Unit::g),
            ),
            new Fruit(
                id: 4,
                name: 'Melons',
                quantity: Quantity::create(amount: 120000, unit: Unit::g),
            ),
            new Fruit(
                id: 8,
                name: 'Berries',
                quantity: Quantity::create(amount: 10000, unit: Unit::g),
            ),
            new Fruit(
                id: 14,
                name: 'Bananas',
                quantity: Quantity::create(amount: 100000, unit: Unit::g),
            ),
            new Fruit(
                id: 15,
                name: 'Oranges',
                quantity: Quantity::create(amount: 24000, unit: Unit::g),
            ),
            new Fruit(
                id: 16,
                name: 'Avocado',
                quantity: Quantity::create(amount: 10000, unit: Unit::g),
            ),
            new Fruit(
                id: 17,
                name: 'Lettuce',
                quantity: Quantity::create(amount: 20830, unit: Unit::g),
            ),
            new Fruit(
                id: 18,
                name: 'Kiwi',
                quantity: Quantity::create(amount: 10000, unit: Unit::g),
            ),
            new Fruit(
                id: 19,
                name: 'Kumquat',
                quantity: Quantity::create(amount: 90000, unit: Unit::g),
            ),
        );

        $this->get(FruitRepository::class)->save($fruits);

        $this->testItListsEdibles($fruits, $queryString, $expectedFruits);
    }
}