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
     * @return array<string, array{Quantity, Unit, float}>
     */
    public static function provideQuantityAndExpectedConversion(): array
    {
        return [
            'kg to kg' => [
                new Quantity(2, Unit::kg),
                Unit::kg,
                2,
            ],
            'kg to gr' => [
                new Quantity(2, Unit::kg),
                Unit::g,
                2000,
            ],
            'gr to gr' => [
                new Quantity(100, Unit::g),
                Unit::g,
                100,
            ],
            'gr to kg' => [
                new Quantity(500, Unit::g),
                Unit::kg,
                0.5,
            ],
        ];
    }

    #[DataProvider('provideQuantityAndExpectedConversion')]
    public function testItDoesCorrectConversions(Quantity $quantity, Unit $convertTo, float $expectedAmount): void
    {
        $conversion = $quantity->convertTo($convertTo);

        $this->assertSame($expectedAmount, $conversion->amount);
    }
}