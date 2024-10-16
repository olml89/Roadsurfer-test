<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Fruit;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Unit;
use PHPUnit\Framework\TestCase;

final class FruitCollectionTest extends TestCase
{
    private FruitCollection $fruitCollection;
    private Fruit $innerFruit;
    private Fruit $outerFruit;

    protected function setUp(): void
    {
        $this->innerFruit = new Fruit(
            id: 2,
            name: 'Apples',
            quantity: Quantity::create(amount: 20000, unit: Unit::g),
        );
        $this->outerFruit = new Fruit(
            id: 3,
            name: 'Pears',
            quantity: Quantity::create(amount: 3500, unit: Unit::g),
        );
        $this->fruitCollection = new FruitCollection($this->innerFruit);
    }

    public function testAddMethod(): void
    {
        $this->fruitCollection->add($this->outerFruit);

        $this->assertEquals(
            [
                $this->innerFruit,
                $this->outerFruit,
            ],
            $this->fruitCollection->list()
        );
    }

    public function testContainsMethod(): void
    {
        $this->assertTrue($this->fruitCollection->contains($this->innerFruit));
        $this->assertFalse($this->fruitCollection->contains($this->outerFruit));
    }

    public function testGetMethod(): void
    {
        $this->assertEquals($this->innerFruit, $this->fruitCollection->get(0));
        $this->assertNull($this->fruitCollection->get(1));
    }

    public function testIndexOfMethod(): void
    {
        $this->assertEquals(0, $this->fruitCollection->indexOf($this->innerFruit));
        $this->assertFalse($this->fruitCollection->indexOf($this->outerFruit));
    }

    public function testPrependMethod(): void
    {
        $this->fruitCollection->prepend($this->outerFruit);

        $this->assertEquals(
            [
                $this->outerFruit,
                $this->innerFruit,
            ],
            $this->fruitCollection->list()
        );
    }

    public function testRemoveMethod(): void
    {
        $this->fruitCollection->remove($this->innerFruit);

        $this->assertEmpty($this->fruitCollection);
    }
}