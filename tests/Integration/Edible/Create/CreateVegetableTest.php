<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\Create;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Infrastructure\Doctrine\EdibleTypeType;
use App\Edible\Infrastructure\Doctrine\UnitType;
use App\Edible\Infrastructure\Doctrine\Vegetable\DoctrineVegetableRepository;
use App\Edible\Infrastructure\Http\Create\CreateEdibleRequestDto;
use App\Edible\Infrastructure\Http\Create\CreateVegetableController;
use App\Edible\Infrastructure\Http\DecidesReturnedUnitsDto;
use App\Edible\Infrastructure\Http\UnitsConverter;
use App\Shared\Infrastructure\Collection\CollectionWrapperNormalizer;
use App\Shared\Infrastructure\Http\KernelExceptionEventSubscriber;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(CreateVegetableController::class)]
#[UsesClass(Edible::class)]
#[UsesClass(Quantity::class)]
#[UsesClass(Unit::class)]
#[UsesClass(Vegetable::class)]
#[UsesClass(EdibleTypeType::class)]
#[UsesClass(UnitType::class)]
#[UsesClass(DoctrineVegetableRepository::class)]
#[UsesClass(CreateEdibleRequestDto::class)]
#[UsesClass(CollectionWrapperNormalizer::class)]
#[UsesClass(KernelExceptionEventSubscriber::class)]
#[UsesClass(UnitsConverter::class)]
#[UsesClass(DecidesReturnedUnitsDto::class)]
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