<?php

declare(strict_types=1);

namespace App\Edible\Domain\Fruit;

use App\Shared\Domain\Collection\Collection;
use App\Shared\Domain\Collection\CollectionWrapper;

final class FruitCollection
{
    /**
     * @use CollectionWrapper<Fruit>
     */
    use CollectionWrapper;

    public function __construct(Fruit ...$items)
    {
        $this->collection = new Collection($items);
    }

    public function add(Fruit ...$items): self
    {
        $this->collection->add(...$items);

        return $this;
    }

    public function contains(Fruit $item): bool
    {
        return $this->collection->contains($item);
    }

    /**
     * @param array-key $index
     */
    public function get(int|string $index): ?Fruit
    {
        return $this->collection->get($index);
    }

    public function indexOf(Fruit $item): int|string|false
    {
        return $this->collection->indexOf($item);
    }

    public function prepend(Fruit ...$items): self
    {
        $this->collection->prepend(...$items);

        return $this;
    }

    public function remove(Fruit $item): self
    {
        $this->collection->remove($item);

        return $this;
    }
}