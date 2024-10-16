<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain;

use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    /**
     * @return array<string, array{Quantity, int, Unit}>
     */
    public static function provideQuantityAndExpectedAmountAndUnit(): array
    {
        return [
            'g' => [
                Quantity::create(amount: 200, unit: Unit::g),
                200,
                Unit::g,
            ],
            'kg' => [
                Quantity::create(amount: 2.12, unit: Unit::kg),
                2120,
                Unit::g,
            ],
        ];
    }

    #[DataProvider('provideQuantityAndExpectedAmountAndUnit')]
    public function testItIsCreatedConvertedToTheLowestUnit(Quantity $quantity, int $expectedAmount, Unit $expectedUnit): void
    {
        $this->assertEquals($expectedAmount, $quantity->amount);
        $this->assertEquals($expectedUnit, $quantity->unit);
    }

    /**
     * @return array<string, array{Quantity, Unit, int|float}>
     */
    public static function provideQuantityAndExpectedFormat(): array
    {
        return [
            'kg to kg' => [
                Quantity::create(2.32, Unit::kg),
                Unit::kg,
                2.32,
            ],
            'kg to gr' => [
                Quantity::create(2.32, Unit::kg),
                Unit::g,
                2320,
            ],
            'gr to gr' => [
                Quantity::create(27732, Unit::g),
                Unit::g,
                27732,
            ],
            'gr to kg' => [
                Quantity::create(27732, Unit::g),
                Unit::kg,
                27.732,
            ],
        ];
    }

    #[DataProvider('provideQuantityAndExpectedFormat')]
    public function testItCanGetConverted(Quantity $quantity, Unit $convertTo, int|float $expectedAmount): void
    {
        $converted = $quantity->convertTo($convertTo);

        $this->assertSame($expectedAmount, $converted->amount);
    }
}