<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\Create;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Infrastructure\Doctrine\EdibleTypeType;
use App\Edible\Infrastructure\Doctrine\Fruit\DoctrineFruitRepository;
use App\Edible\Infrastructure\Doctrine\UnitType;
use App\Edible\Infrastructure\Http\Create\CreateEdibleRequestDto;
use App\Edible\Infrastructure\Http\Create\CreateFruitController;
use App\Edible\Infrastructure\Http\DecidesReturnedUnitsDto;
use App\Edible\Infrastructure\Http\UnitsConverter;
use App\Shared\Infrastructure\Collection\CollectionWrapperNormalizer;
use App\Shared\Infrastructure\Http\KernelExceptionEventSubscriber;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(CreateFruitController::class)]
#[UsesClass(Edible::class)]
#[UsesClass(Quantity::class)]
#[UsesClass(Unit::class)]
#[UsesClass(Fruit::class)]
#[UsesClass(EdibleTypeType::class)]
#[UsesClass(UnitType::class)]
#[UsesClass(DoctrineFruitRepository::class)]
#[UsesClass(CreateEdibleRequestDto::class)]
#[UsesClass(CollectionWrapperNormalizer::class)]
#[UsesClass(KernelExceptionEventSubscriber::class)]
#[UsesClass(UnitsConverter::class)]
#[UsesClass(DecidesReturnedUnitsDto::class)]
final class CreateFruitTest extends KernelTestCase
{
    use TestsEdibleCreationEndpoint;

    protected function getEndpoint(): string
    {
        return '/fruits';
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
    public function testItCreatesFruit(?Unit $convertTo): void
    {
        $this->testItCreatesEdible(Type::Fruit, $convertTo);
    }
}