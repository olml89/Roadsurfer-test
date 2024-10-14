<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Edible\Domain\Vegetable\VegetableRepository;
use App\Edible\Infrastructure\Doctrine\EdibleTypeType;
use App\Edible\Infrastructure\Doctrine\UnitType;
use App\Edible\Infrastructure\Doctrine\Vegetable\DoctrineVegetableRepository;
use App\Edible\Infrastructure\Http\ListVegetableController;
use App\Shared\Domain\Collection\Collection;
use App\Shared\Infrastructure\Collection\CollectionWrapperNormalizer;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

#[CoversClass(ListVegetableController::class)]
#[UsesClass(Edible::class)]
#[UsesClass(Vegetable::class)]
#[UsesClass(VegetableCollection::class)]
#[UsesClass(Collection::class)]
#[UsesClass(Quantity::class)]
#[UsesClass(Unit::class)]
#[UsesClass(DoctrineVegetableRepository::class)]
#[UsesClass(EdibleTypeType::class)]
#[UsesClass(UnitType::class)]
#[UsesClass(CollectionWrapperNormalizer::class)]
final class ListVegetablesTest extends KernelTestCase
{
    private VegetableRepository $vegetableRepository;
    private KernelBrowser $client;
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->client = new KernelBrowser(self::bootKernel());
        $this->vegetableRepository = $this->get(VegetableRepository::class);
        $this->serializer = $this->get(SerializerInterface::class);
    }

    public function testItListsVegetables(): void
    {
        $vegetables = new VegetableCollection(
            new Vegetable(
                id: 1,
                name: 'Beans',
                quantity: new Quantity(amount: 65000, unit: Unit::g),
            ),
            new Vegetable(
                id: 2,
                name: 'Broccoli',
                quantity: new Quantity(amount: 3, unit: Unit::kg),
            ),
        );

        $this->vegetableRepository->save($vegetables);

        $this
            ->client
            ->request('GET', '/vegetables');

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals(
            $this->serializer->serialize($vegetables, 'json'),
            $response->getContent(),
        );
    }
}