<?php

declare(strict_types=1);

namespace App\Edible\Domain\Vegetable;

use App\Shared\Domain\Collection\Collection;
use App\Shared\Domain\Collection\CollectionWrapper;

final class VegetableCollection
{
    /**
     * @use CollectionWrapper<Vegetable>
     */
    use CollectionWrapper;

    public function __construct(Vegetable ...$items)
    {
        $this->collection = new Collection($items);
    }

    public function add(Vegetable ...$items): self
    {
        $this->collection->add(...$items);

        return $this;
    }

    public function contains(Vegetable $item): bool
    {
        return $this->collection->contains($item);
    }

    /**
     * @param array-key $index
     */
    public function get(int|string $index): ?Vegetable
    {
        return $this->collection->get($index);
    }

    public function indexOf(Vegetable $item): int|string|false
    {
        return $this->collection->indexOf($item);
    }

    public function prepend(Vegetable ...$items): self
    {
        $this->collection->prepend(...$items);

        return $this;
    }

    public function remove(Vegetable $item): self
    {
        $this->collection->remove($item);

        return $this;
    }
}