<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Specification;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Specification\QuantityComparesTo;
use App\Edible\Domain\Unit;
use App\Shared\Domain\Criteria\Filter\Operator;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class QuantityComparesToTest extends TestCase
{
    /**
     * @return array<int, array{0: Edible, 1: QuantityComparesTo, 2: bool}>
     */
    public static function provideEdibleSpecificationAndExpectedResult(): array
    {
        return [
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 100, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::LIKE,
                    Quantity::create(amount: 100, unit: Unit::g),
                ),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 100, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::EQ,
                    Quantity::create(amount: 100, unit: Unit::g),
                ),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 50, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::NEQ,
                    Quantity::create(amount: 100, unit: Unit::g),
                ),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 100, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::LT,
                    Quantity::create(amount: 100, unit: Unit::g),
                ),
                false,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 100, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::LTE,
                    Quantity::create(amount: 100, unit: Unit::g),
                ),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 100, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::GT,
                    Quantity::create(amount: 100, unit: Unit::g),
                ),
                false,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 100, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::GTE,
                    Quantity::create(amount: 100, unit: Unit::g),
                ),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 100, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::IN,
                    Quantity::create(amount: 100, unit: Unit::g),
                    Quantity::create(amount: 50, unit: Unit::g),
                    Quantity::create(amount: 150, unit: Unit::g),
                ),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    quantity: Quantity::create(amount: 120, unit: Unit::g),
                ),
                new QuantityComparesTo(
                    Operator::NIN,
                    Quantity::create(amount: 100, unit: Unit::g),
                    Quantity::create(amount: 50, unit: Unit::g),
                    Quantity::create(amount: 150, unit: Unit::g),
                ),
                true,
            ],
        ];
    }

    #[DataProvider('provideEdibleSpecificationAndExpectedResult')]
    public function testItIsSatisfiedBy(Edible $edible, QuantityComparesTo $quantityComparesTo, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $quantityComparesTo->isSatisfiedBy($edible));
    }
}