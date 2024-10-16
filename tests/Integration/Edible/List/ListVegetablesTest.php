<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\List;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Specification\EdibleAndSpecification;
use App\Edible\Domain\Specification\EdibleOrSpecification;
use App\Edible\Domain\Specification\NameContains;
use App\Edible\Domain\Specification\QuantityComparesTo;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Edible\Domain\Vegetable\VegetableRepository;
use App\Edible\Infrastructure\Doctrine\EdibleTypeType;
use App\Edible\Infrastructure\Doctrine\UnitType;
use App\Edible\Infrastructure\Doctrine\Vegetable\DoctrineVegetableRepository;
use App\Edible\Infrastructure\Http\List\ListEdibleRequestDto;
use App\Edible\Infrastructure\Http\List\ListVegetableController;
use App\Edible\Infrastructure\Http\List\QuantityDto;
use App\Shared\Domain\Collection\Collection;
use App\Shared\Domain\Criteria\CompositeExpression\AndExpression;
use App\Shared\Domain\Criteria\CompositeExpression\CompositeExpression;
use App\Shared\Domain\Criteria\CompositeExpression\OrExpression;
use App\Shared\Domain\Criteria\CompositeExpression\Type;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\EqualTo;
use App\Shared\Domain\Criteria\Filter\Filter;
use App\Shared\Domain\Criteria\Filter\GreaterThan;
use App\Shared\Domain\Criteria\Filter\GreaterThanOrEqualTo;
use App\Shared\Domain\Criteria\Filter\In;
use App\Shared\Domain\Criteria\Filter\LessThanOrEqualTo;
use App\Shared\Domain\Criteria\Filter\Like;
use App\Shared\Domain\Criteria\Filter\Operator;
use App\Shared\Domain\ValidationException;
use App\Shared\Infrastructure\Collection\CollectionWrapperNormalizer;
use App\Shared\Infrastructure\Doctrine\DoctrineCriteriaConverter;
use App\Shared\Infrastructure\Http\KernelExceptionEventSubscriber;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(ListVegetableController::class)]
#[UsesClass(Edible::class)]
#[UsesClass(Vegetable::class)]
#[UsesClass(VegetableCollection::class)]
#[UsesClass(Collection::class)]
#[UsesClass(Quantity::class)]
#[UsesClass(Unit::class)]
#[UsesClass(DoctrineVegetableRepository::class)]
#[UsesClass(EdibleTypeType::class)]
#[UsesClass(UnitType::class)]
#[UsesClass(CollectionWrapperNormalizer::class)]
#[UsesClass(EdibleAndSpecification::class)]
#[UsesClass(EdibleOrSpecification::class)]
#[UsesClass(NameContains::class)]
#[UsesClass(QuantityComparesTo::class)]
#[UsesClass(ListEdibleRequestDto::class)]
#[UsesClass(QuantityDto::class)]
#[UsesClass(CompositeExpression::class)]
#[UsesClass(AndExpression::class)]
#[UsesClass(OrExpression::class)]
#[UsesClass(Criteria::class)]
#[UsesClass(Filter::class)]
#[UsesClass(Operator::class)]
#[UsesClass(Like::class)]
#[UsesClass(EqualTo::class)]
#[UsesClass(GreaterThan::class)]
#[UsesClass(GreaterThanOrEqualTo::class)]
#[UsesClass(LessThanOrEqualTo::class)]
#[UsesClass(In::class)]
#[UsesClass(DoctrineCriteriaConverter::class)]
#[UsesClass(ValidationException::class)]
#[UsesClass(KernelExceptionEventSubscriber::class)]
final class ListVegetablesTest extends KernelTestCase
{
    use TestsEdibleListingEndpoint {
        TestsEdibleListingEndpoint::setUp as baseSetUp;
    }

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
                        quantity: new Quantity(amount: 65000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 6,
                        name: 'Beetroot',
                        quantity: new Quantity(amount: 950, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 7,
                        name: 'Broccoli',
                        quantity: new Quantity(amount: 3000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: new Quantity(amount: 500000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 13,
                        name: 'Cucumber',
                        quantity: new Quantity(amount: 8000, unit: Unit::g)
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
                        quantity: new Quantity(amount: 50000, unit: Unit::g)
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
                        quantity: new Quantity(amount: 65000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: new Quantity(amount: 500000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 12,
                        name: 'Onion',
                        quantity: new Quantity(amount: 50000, unit: Unit::g)
                    ),
                        new Vegetable(
                        id: 20,
                        name: 'Pepper',
                        quantity: new Quantity(amount: 150000, unit: Unit::g)
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
                        quantity: new Quantity(amount: 950, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 7,
                        name: 'Broccoli',
                        quantity: new Quantity(amount: 3000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 9,
                        name: 'Tomatoes',
                        quantity: new Quantity(amount: 5000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 13,
                        name: 'Cucumber',
                        quantity: new Quantity(amount: 8000, unit: Unit::g)
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
                        quantity: new Quantity(amount: 65000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: new Quantity(amount: 500000, unit: Unit::g)
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
                        quantity: new Quantity(amount: 5000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 13,
                        name: 'Cucumber',
                        quantity: new Quantity(amount: 8000, unit: Unit::g)
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
                        quantity: new Quantity(amount: 10922, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 6,
                        name: 'Beetroot',
                        quantity: new Quantity(amount: 950, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 9,
                        name: 'Tomatoes',
                        quantity: new Quantity(amount: 5000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 11,
                        name: 'Cabbage',
                        quantity: new Quantity(amount: 500000, unit: Unit::g)
                    ),
                    new Vegetable(
                        id: 20,
                        name: 'Pepper',
                        quantity: new Quantity(amount: 150000, unit: Unit::g)
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
                quantity: new Quantity(amount: 10922, unit: Unit::g)
            ),
            new Vegetable(
                id: 5,
                name: 'Beans',
                quantity: new Quantity(amount: 65000, unit: Unit::g)
            ),
            new Vegetable(
                id: 6,
                name: 'Beetroot',
                quantity: new Quantity(amount: 950, unit: Unit::g)
            ),
            new Vegetable(
                id: 7,
                name: 'Broccoli',
                quantity: new Quantity(amount: 3000, unit: Unit::g)
            ),
            new Vegetable(
                id: 9,
                name: 'Tomatoes',
                quantity: new Quantity(amount: 5000, unit: Unit::g)
            ),
            new Vegetable(
                id: 10,
                name: 'Celery',
                quantity: new Quantity(amount: 20000, unit: Unit::g)
            ),
            new Vegetable(
                id: 11,
                name: 'Cabbage',
                quantity: new Quantity(amount: 500000, unit: Unit::g)
            ),
            new Vegetable(
                id: 12,
                name: 'Onion',
                quantity: new Quantity(amount: 50000, unit: Unit::g)
            ),
            new Vegetable(
                id: 13,
                name: 'Cucumber',
                quantity: new Quantity(amount: 8000, unit: Unit::g)
            ),
            new Vegetable(
                id: 20,
                name: 'Pepper',
                quantity: new Quantity(amount: 150000, unit: Unit::g)
            ),
        );

        $this->get(VegetableRepository::class)->save($vegetables);

        $this->testItListsEdibles($vegetables, $queryString, $expectedVegetables);
    }
}