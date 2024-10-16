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
use App\Edible\Infrastructure\Http\Create\CreateFruitController;
use App\Edible\Infrastructure\Http\Create\CreateVegetableController;
use App\Shared\Infrastructure\Collection\CollectionWrapperNormalizer;
use App\Shared\Infrastructure\Http\KernelExceptionEventSubscriber;
use App\Tests\Helpers\ProvidesEdibleCreationData;
use App\Tests\KernelTestCase;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

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
final class CreateVegetableTest extends KernelTestCase
{
    use TestsEdibleCreationEndpoint;

    protected function getEndpoint(): string
    {
        return '/vegetables';
    }

    public function testItCreatesVegetable(): void
    {
        $this->testItCreatesEdible(Type::Vegetable);
    }
}