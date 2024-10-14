<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain;

use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Quantity::class)]
#[UsesClass(Unit::class)]
final class QuantityTest extends TestCase
{
    /**
     * @return array<string, array{Quantity, Unit, string}>
     */
    public static function provideQuantityAndExpectedAmountAndUnit(): array
    {
        return [
            'g' => [
                new Quantity(amount: 200, unit: Unit::g),
                200,
                Unit::g,
            ],
            'kg' => [
                new Quantity(amount: 2.12, unit: Unit::kg),
                2120,
                Unit::g,
            ],
        ];
    }

    #[DataProvider('provideQuantityAndExpectedAmountAndUnit')]
    public function testItAlwaysGetsConvertedToTheLowestUnit(Quantity $quantity, int $expectedAmount, Unit $expectedUnit): void
    {
        $this->assertEquals($expectedAmount, $quantity->amount);
        $this->assertEquals($expectedUnit, $quantity->unit);
    }

    /**
     * @return array<string, array{Quantity, Unit, string}>
     */
    public static function provideQuantityAndExpectedFormat(): array
    {
        return [
            'kg to kg' => [
                new Quantity(2, Unit::kg),
                Unit::kg,
                '2 kg',
            ],
            'kg to gr' => [
                new Quantity(2, Unit::kg),
                Unit::g,
                '2000 g',
            ],
            'gr to gr' => [
                new Quantity(100, Unit::g),
                Unit::g,
                '100 g',
            ],
            'gr to kg' => [
                new Quantity(500, Unit::g),
                Unit::kg,
                '0.5 kg',
            ],
        ];
    }

    #[DataProvider('provideQuantityAndExpectedFormat')]
    public function testItDoesCorrectFormattings(Quantity $quantity, Unit $formatTo, string $expectedFormat): void
    {
        $format = $quantity->format($formatTo);

        $this->assertSame($expectedFormat, $format);
    }
}