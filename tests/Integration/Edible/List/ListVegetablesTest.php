<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\List;

use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Edible\Domain\Vegetable\VegetableRepository;
use App\Shared\Domain\Criteria\CompositeExpression\Type;
use App\Shared\Domain\Criteria\Filter\Operator;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ListVegetablesTest extends KernelTestCase
{
    use TestsEdibleListingEndpoint;

    protected function getEndpoint(): string
    {
        return '/vegetables';
    }

    /**
     * @return array<string, empty|array{0: array<string, mixed>}|array{0: array<string, mixed>, 1: VegetableCollection}>>
     */
    public static function provideExpectedVegetables(): array
    {
        return [
            'no filters' => [

            ],
            'name filter' => [
                [
                    'name' => 'B',
                ],
                new VegetableCollection(
                    new Vegetable(
                        id: 5,
                        name: 'Beans',
                        quantity: Quantity::create(amount: 65000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 6,
                        name: 'Beetroot',
                        quantity: Quantity::create(amount: 950, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 7,
                        name: 'Broccoli',
                        quantity: Quantity::create(amount: 3000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: Quantity::create(amount: 500000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 13,
                        name: 'Cucumber',
                        quantity: Quantity::create(amount: 8000, unit: Unit::g)
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
                        'amount' => 50000,
                    ],
                ],
                new VegetableCollection(
                    new Vegetable(
                        id: 12,
                        name: 'Onion',
                        quantity: Quantity::create(amount: 50000, unit: Unit::g)
                    ),
                ),
            ],
            'quantity filter with operator' => [
                [
                    'quantity' => [
                        'amount' => 50000,
                        'op' => Operator::GTE->value,
                    ],
                ],
                new VegetableCollection(
                    new Vegetable(
                        id: 5,
                        name: 'Beans',
                        quantity: Quantity::create(amount: 65000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: Quantity::create(amount: 500000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 12,
                        name: 'Onion',
                        quantity: Quantity::create(amount: 50000, unit: Unit::g)
                    ),
                        new Vegetable(
                        id: 20,
                        name: 'Pepper',
                        quantity: Quantity::create(amount: 150000, unit: Unit::g)
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
                new VegetableCollection(
                    new Vegetable(
                        id: 6,
                        name: 'Beetroot',
                        quantity: Quantity::create(amount: 950, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 7,
                        name: 'Broccoli',
                        quantity: Quantity::create(amount: 3000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 9,
                        name: 'Tomatoes',
                        quantity: Quantity::create(amount: 5000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 13,
                        name: 'Cucumber',
                        quantity: Quantity::create(amount: 8000, unit: Unit::g)
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
                new VegetableCollection(
                    new Vegetable(
                        id: 5,
                        name: 'Beans',
                        quantity: Quantity::create(amount: 65000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: Quantity::create(amount: 500000, unit: Unit::g)
                    ),
                ),
            ],
            'quantity amount with a composite value' => [
                [
                    'quantity' => [
                        'amount' => '5,8',
                        'op' => Operator::IN->value,
                        'unit' => Unit::kg->value,
                    ],
                ],
                new VegetableCollection(
                    new Vegetable(
                        id: 9,
                        name: 'Tomatoes',
                        quantity: Quantity::create(amount: 5000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 13,
                        name: 'Cucumber',
                        quantity: Quantity::create(amount: 8000, unit: Unit::g)
                    ),
                ),
            ],
            'name and quantity filters joined by an OR operator' => [
                [
                    'name' => 'T',
                    'op' => Type::OR->value,
                    'quantity' => [
                        'amount' => 100,
                        'op' => Operator::GT->value,
                        'unit' => Unit::kg->value,
                    ],
                ],
                new VegetableCollection(
                    new Vegetable(
                        id: 1,
                        name: 'Carrot',
                        quantity: Quantity::create(amount: 10922, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 6,
                        name: 'Beetroot',
                        quantity: Quantity::create(amount: 950, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 9,
                        name: 'Tomatoes',
                        quantity: Quantity::create(amount: 5000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: Quantity::create(amount: 500000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 20,
                        name: 'Pepper',
                        quantity: Quantity::create(amount: 150000, unit: Unit::g)
                    ),
                ),
            ],
            'Pepper converted to gr' => [
                [
                    'name' => 'Pepper',
                    'unit' => Unit::g->value,
                ],
                new VegetableCollection(
                    new Vegetable(
                        id: 20,
                        name: 'Pepper',
                        quantity: Quantity::create(amount: 150000, unit: Unit::g)
                    ),
                ),
            ],
            'Pepper converted to kg' => [
                [
                    'name' => 'Pepper',
                    'unit' => Unit::kg->value,
                ],
                new VegetableCollection(
                    new Vegetable(
                        id: 20,
                        name: 'Pepper',
                        quantity: Quantity::create(amount: 150000, unit: Unit::g)->convertTo(Unit::kg),
                    ),
                ),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $queryString
     */
    #[DataProvider('provideExpectedVegetables')]
    public function testItListsVegetables(array $queryString = [], ?VegetableCollection $expectedVegetables = null): void
    {
        $vegetables = new VegetableCollection(
            new Vegetable(
                id: 1,
                name: 'Carrot',
                quantity: Quantity::create(amount: 10922, unit: Unit::g)
            ),
            new Vegetable(
                id: 5,
                name: 'Beans',
                quantity: Quantity::create(amount: 65000, unit: Unit::g)
            ),
            new Vegetable(
                id: 6,
                name: 'Beetroot',
                quantity: Quantity::create(amount: 950, unit: Unit::g)
            ),
            new Vegetable(
                id: 7,
                name: 'Broccoli',
                quantity: Quantity::create(amount: 3000, unit: Unit::g)
            ),
            new Vegetable(
                id: 9,
                name: 'Tomatoes',
                quantity: Quantity::create(amount: 5000, unit: Unit::g)
            ),
            new Vegetable(
                id: 10,
                name: 'Celery',
                quantity: Quantity::create(amount: 20000, unit: Unit::g)
            ),
            new Vegetable(
                id: 11,
                name: 'Cabbage',
                quantity: Quantity::create(amount: 500000, unit: Unit::g)
            ),
            new Vegetable(
                id: 12,
                name: 'Onion',
                quantity: Quantity::create(amount: 50000, unit: Unit::g)
            ),
            new Vegetable(
                id: 13,
                name: 'Cucumber',
                quantity: Quantity::create(amount: 8000, unit: Unit::g)
            ),
            new Vegetable(
                id: 20,
                name: 'Pepper',
                quantity: Quantity::create(amount: 150000, unit: Unit::g)
            ),
        );

        $this->get(VegetableRepository::class)->save($vegetables);

        $this->testItListsEdibles($vegetables, $queryString, $expectedVegetables);
    }
}