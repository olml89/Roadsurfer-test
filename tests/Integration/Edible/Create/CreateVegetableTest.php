<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\Create;

use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreateVegetableTest extends KernelTestCase
{
    use TestsEdibleCreationEndpoint;

    protected function getEndpoint(): string
    {
        return '/vegetables';
    }

    /**
     * @return array<int, array<int, ?Unit>>
     */
    public static function provideRequestedUnitOptions(): array
    {
        return [
            [
                null,
            ],
            [
                Unit::g,
            ],
            [
                Unit::kg,
            ],
        ];
    }

    #[DataProvider('provideRequestedUnitOptions')]
    public function testItCreatesVegetable(?Unit $convertTo): void
    {
        $this->testItCreatesEdible(Type::Vegetable, $convertTo);
    }
}