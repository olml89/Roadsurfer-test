<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\Collection;

use App\Shared\Domain\Collection\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Collection::class)]
final class CollectionTest extends TestCase
{
    /**
     * @var Collection<int>
     */
    private Collection $collection;

    protected function setUp(): void
    {
        $this->collection = new Collection([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);
    }

    public function testAddMethod(): void
    {
        $this->collection->add(10);

        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $this->collection->list());
    }

    public function testClearMethod(): void
    {
        $this->collection->clear();

        $this->assertEquals([], $this->collection->list());
    }

    public function testContainsMethod(): void
    {
        $this->assertTrue($this->collection->contains(9));
        $this->assertFalse($this->collection->contains(10));
    }

    public function testCountMethod(): void
    {
        $this->assertCount(10, $this->collection);
        $this->assertEquals(10, $this->collection->count());
    }

    public function testEachMethod(): void
    {
        $sum = 0;

        $this->collection->each(
            function (int $item) use(&$sum): void {
                $sum += $item;
            }
        );

        $this->assertEquals(45, $sum);
    }

    public function testFilterMethod(): void
    {
        $filtered = $this->collection->filter(fn (int $item): bool => $item > 4);

        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $this->collection->list());
        $this->assertEquals([5, 6, 7, 8, 9], $filtered->list());
    }

    public function testGetMethod(): void
    {
        $this->assertEquals(9, $this->collection->get(9));
        $this->assertNull($this->collection->get(10));
    }

    public function testIndexOfMethod(): void
    {
        $this->assertEquals(4, $this->collection->indexOf(4));
    }

    public function testIsEmptyMethod(): void
    {
        $emptyCollection = new Collection();

        $this->assertFalse($this->collection->isEmpty());
        $this->assertTrue($emptyCollection->isEmpty());
    }

    public function testListMethod(): void
    {
        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $this->collection->list());
    }

    public function testMapMethod(): void
    {
        $mapped = $this->collection->map(fn (int $item): string => (string)$item);

        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $this->collection->list());
        $this->assertEquals(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $mapped->list());
    }

    public function testPrependMethod(): void
    {
        $this->collection->prepend(-1);

        $this->assertEquals([-1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $this->collection->list());
    }

    public function testReduceMethod(): void
    {
        $result = $this->collection->reduce(
            fn(int $carry, int $item): int => $carry - $item,
            initial: 0,
        );

        $this->assertEquals(-45, $result);
    }

    public function testRemoveMethod(): void
    {
        $this->collection->remove(4);

        $this->assertEquals([0, 1, 2, 3, 5, 6, 7, 8, 9], $this->collection->list());
    }

    public function testRemoveAtMethod(): void
    {
        $this->collection->removeAt(9);

        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8], $this->collection->list());
    }

    public function testTransformMethod(): void
    {
        $this->collection->transform(fn (int $item): bool => $item % 2 === 0);

        $this->assertEquals([true, false, true, false, true, false, true, false, true, false], $this->collection->list());
    }

    public function testValuesMethod(): void
    {
        $hashMap = new Collection([
            'first' => 0,
            'second' => 1,
        ]);

        $this->assertEquals([0, 1], $hashMap->values()->list());
    }
}