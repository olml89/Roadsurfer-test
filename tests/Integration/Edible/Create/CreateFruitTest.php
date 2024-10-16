<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\Create;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Infrastructure\Doctrine\EdibleTypeType;
use App\Edible\Infrastructure\Doctrine\Fruit\DoctrineFruitRepository;
use App\Edible\Infrastructure\Doctrine\UnitType;
use App\Edible\Infrastructure\Doctrine\Vegetable\DoctrineVegetableRepository;
use App\Edible\Infrastructure\Http\Create\CreateEdibleRequestDto;
use App\Edible\Infrastructure\Http\Create\CreateFruitController;
use App\Shared\Infrastructure\Collection\CollectionWrapperNormalizer;
use App\Shared\Infrastructure\Http\KernelExceptionEventSubscriber;
use App\Tests\Helpers\ProvidesEdibleCreationData;
use App\Tests\KernelTestCase;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

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
final class CreateFruitTest extends KernelTestCase
{
    use TestsEdibleCreationEndpoint;

    protected function getEndpoint(): string
    {
        return '/fruits';
    }

    public function testItCreatesFruit(): void
    {
        $this->testItCreatesEdible(Type::Fruit);
    }
}