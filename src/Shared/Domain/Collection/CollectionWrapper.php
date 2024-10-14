<?php

declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use Closure;


/**
 * @template T
 */
abstract class CollectionWrapper
{
    /**
     * @var Collection<T>
     */
    protected Collection $collection;

    /**
     * @param array<array-key, T> $items
     */
    public function __construct(array $items)
    {
        $this->collection = new Collection($items);
    }

    public function clear(): static
    {
        $this->collection->clear();

        return $this;
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    /**
     * @param Closure(T): void $callback
     */
    public function each(Closure $callback): static
    {
        $this->collection->each($callback);

        return $this;
    }

    /**
     * @param Closure(T, array-key): bool $callable
     */
    public function filter(Closure $callable): static
    {
        $this->collection = $this->collection->filter($callable);

        return $this;
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }

    /**
     * @return array<array-key, T>
     */
    public function list(): array
    {
        return $this->collection->list();
    }

    /**
     * @param Closure(T): mixed $callable
     * @return Collection<mixed>
     */
    public function map(Closure $callable): Collection
    {
        return $this->collection->map($callable);
    }

    /**
     * @template R
     * @param Closure(R, T): R $callable
     * @return R
     */
    public function reduce(Closure $callable): mixed
    {
        return $this->collection->reduce($callable);
    }

    /**
     * @param array-key $index
     */
    public function removeAt(int|string $index): static
    {
        $this->collection->removeAt($index);

        return $this;
    }

    /**
     * @param Closure(T): mixed $callable
     */
    public function transform(Closure $callable): static
    {
        $this->collection->transform($callable);

        return $this;
    }

    public function values(): static
    {
        $this->collection = $this->collection->values();

        return $this;
    }
}