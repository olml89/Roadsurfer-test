<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Vegetable;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use PHPUnit\Framework\TestCase;

final class VegetableCollectionTest extends TestCase
{
    private VegetableCollection $vegetableCollection;
    private Vegetable $innerVegetable;
    private Vegetable $outerVegetable;

    protected function setUp(): void
    {
        $this->innerVegetable = new Vegetable(
            id: 1,
            name: 'Carrot',
            quantity: Quantity::create(amount: 10922, unit: Unit::g)
        );
        $this->outerVegetable = new Vegetable(
            id: 5,
            name: 'Beans',
            quantity: Quantity::create(amount: 65000, unit: Unit::g)
        );
        $this->vegetableCollection = new VegetableCollection($this->innerVegetable);
    }

    public function testAddMethod(): void
    {
        $this->vegetableCollection->add($this->outerVegetable);

        $this->assertEquals(
            [
                $this->innerVegetable,
                $this->outerVegetable,
            ],
            $this->vegetableCollection->list()
        );
    }

    public function testContainsMethod(): void
    {
        $this->assertTrue($this->vegetableCollection->contains($this->innerVegetable));
        $this->assertFalse($this->vegetableCollection->contains($this->outerVegetable));
    }

    public function testGetMethod(): void
    {
        $this->assertEquals($this->innerVegetable, $this->vegetableCollection->get(0));
        $this->assertNull($this->vegetableCollection->get(1));
    }

    public function testIndexOfMethod(): void
    {
        $this->assertEquals(0, $this->vegetableCollection->indexOf($this->innerVegetable));
        $this->assertFalse($this->vegetableCollection->indexOf($this->outerVegetable));
    }

    public function testPrependMethod(): void
    {
        $this->vegetableCollection->prepend($this->outerVegetable);

        $this->assertEquals(
            [
                $this->outerVegetable,
                $this->innerVegetable,
            ],
            $this->vegetableCollection->list()
        );
    }

    public function testRemoveMethod(): void
    {
        $this->vegetableCollection->remove($this->innerVegetable);

        $this->assertEmpty($this->vegetableCollection);
    }
}