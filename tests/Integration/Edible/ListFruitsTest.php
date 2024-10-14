<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Fruit\FruitRepository;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use App\Edible\Infrastructure\Doctrine\EdibleTypeType;
use App\Edible\Infrastructure\Doctrine\Fruit\DoctrineFruitRepository;
use App\Edible\Infrastructure\Doctrine\UnitType;
use App\Edible\Infrastructure\Http\ListFruitController;
use App\Shared\Domain\Collection\Collection;
use App\Shared\Infrastructure\Collection\CollectionWrapperNormalizer;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

#[CoversClass(ListFruitController::class)]
#[UsesClass(Edible::class)]
#[UsesClass(Fruit::class)]
#[UsesClass(FruitCollection::class)]
#[UsesClass(Collection::class)]
#[UsesClass(Quantity::class)]
#[UsesClass(Unit::class)]
#[UsesClass(DoctrineFruitRepository::class)]
#[UsesClass(EdibleTypeType::class)]
#[UsesClass(UnitType::class)]
#[UsesClass(CollectionWrapperNormalizer::class)]
final class ListFruitsTest extends KernelTestCase
{
    private FruitRepository $fruitRepository;
    private KernelBrowser $client;
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->client = new KernelBrowser(self::bootKernel());
        $this->fruitRepository = $this->get(FruitRepository::class);
        $this->serializer = $this->get(SerializerInterface::class);
    }

    public function testItListsFruits(): void
    {
        $fruits = new FruitCollection(
            new Fruit(
                id: 1,
                name: 'Bananas',
                quantity: new Quantity(amount: 3, unit: Unit::kg),
            ),
            new Fruit(
                id: 2,
                name: 'Oranges',
                quantity: new Quantity(amount: 5.5, unit: Unit::kg),
            ),
        );

        $this->fruitRepository->save($fruits);

        $this
            ->client
            ->request('GET', '/fruits');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals(
            $this->serializer->serialize($fruits, 'json'),
            $response->getContent(),
        );
    }
}