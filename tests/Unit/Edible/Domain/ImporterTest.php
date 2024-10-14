<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Importer;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Shared\Domain\Collection\Collection;
use App\Tests\Unit\Edible\Domain\Fixtures\ArrayDataProvider;
use App\Tests\Unit\Edible\Domain\Fixtures\InMemoryFruitRepository;
use App\Tests\Unit\Edible\Domain\Fixtures\InMemoryVegetableRepository;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Importer::class)]
#[UsesClass(Collection::class)]
#[UsesClass(FruitCollection::class)]
#[UsesClass(VegetableCollection::class)]
#[UsesClass(Edible::class)]
#[UsesClass(Fruit::class)]
#[UsesClass(Vegetable::class)]
#[UsesClass(Quantity::class)]
final class ImporterTest extends TestCase
{
    /**
     * @return array<string, array<array<array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>}>>>
     */
    public static function provideEdibleFeed(): array
    {
        return [
            'empty feed' => [
                [],
            ],
            'non empty feed' => [
                [
                    [
                        'id' => 1,
                        'name' => 'Fruit 1',
                        'type' => Type::Fruit->value,
                        'quantity' => 15,
                        'unit' => Unit::kg->value,
                    ],
                    [
                        'id' => 2,
                        'name' => 'Vegetable 1',
                        'type' => Type::Vegetable->value,
                        'quantity' => 400,
                        'unit' => Unit::g->value,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>}> $edibleFeed
     */
    #[DataProvider('provideEdibleFeed')]
    public function testItReturnsCountOfImportedEdibles(array $edibleFeed): void
    {
        $importer = new Importer(
            dataProvider: new ArrayDataProvider($edibleFeed),
            edibleFactory: new NotValidatedEdibleFactory(),
            fruitRepository: $fruitRepository = new InMemoryFruitRepository(),
            vegetableRepository: $vegetableRepository = new InMemoryVegetableRepository(),
        );

        $expectedFruit = $edibleFeed[0];
        $expectedVegetable = $edibleFeed[1];

        $count = $importer->import('fake_file.json');

        $this->assertEquals(count($edibleFeed), $count);

        if (!is_null($expectedFruit)) {
            $this->assertNotNull($fruit = $fruitRepository->get($expectedFruit['id']));
            $this->assertEquals(
                $expectedFruit['id'],
                $fruit->getId()
            );
            $this->assertEquals(
                $expectedFruit['name'],
                $fruit->getName()
            );
            $this->assertEquals(
                sprintf('%s %s', $expectedFruit['quantity'], $expectedFruit['unit']),
                $fruit->getQuantity()->format(Unit::from($expectedFruit['unit']))
            );
        }

        if (!is_null($expectedVegetable)) {
            $this->assertNotNull($vegetable = $vegetableRepository->get($expectedVegetable['id']));
            $this->assertEquals(
                $expectedVegetable['id'],
                $vegetable->getId()
            );
            $this->assertEquals(
                $expectedVegetable['name'],
                $vegetable->getName()
            );
            $this->assertEquals(
                sprintf('%s %s', $expectedVegetable['quantity'], $expectedVegetable['unit']),
                $vegetable->getQuantity()->format(Unit::from($expectedVegetable['unit']))
            );
        }
    }
}