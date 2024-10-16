<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain;

use App\Edible\Domain\Importer;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Tests\Unit\Edible\Domain\Fixtures\ArrayDataProvider;
use App\Tests\Unit\Edible\Domain\Fixtures\InMemoryFruitRepository;
use App\Tests\Unit\Edible\Domain\Fixtures\InMemoryVegetableRepository;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

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
                $fruit->getId(),
            );
            $this->assertEquals(
                $expectedFruit['name'],
                $fruit->getName(),
            );
            $this->assertEquals(
                $expectedFruit['quantity'],
                $fruit->getQuantity()->convertTo(Unit::from($expectedFruit['unit']))->amount,
            );
            $this->assertEquals(
                Unit::g,
                $fruit->getQuantity()->unit,
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
                $expectedVegetable['quantity'],
                $vegetable->getQuantity()->convertTo(Unit::from($expectedVegetable['unit']))->amount,
            );
            $this->assertEquals(
                Unit::g,
                $vegetable->getQuantity()->unit,
            );
        }
    }
}