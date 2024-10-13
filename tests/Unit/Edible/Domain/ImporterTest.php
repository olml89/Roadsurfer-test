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
    public static function provideEdibleFeedAndExpectedImportedCount(): array
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
    #[DataProvider('provideEdibleFeedAndExpectedImportedCount')]
    public function testItReturnsCountOfImportedEdibles(array $edibleFeed): void
    {
        $importer = new Importer(
            dataProvider: new ArrayDataProvider($edibleFeed),
            edibleFactory: new NotValidatedEdibleFactory(),
        );

        $count = $importer->import('fake_file.json');

        $this->assertEquals(count($edibleFeed), $count);
    }
}