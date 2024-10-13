<?php

declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use Closure;


/**
 * @template T
 */
trait CollectionWrapper
{
    /**
     * @var Collection<T>
     */
    protected Collection $collection;

    public function clear(): self
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
    public function each(Closure $callback): self
    {
        $this->collection->each($callback);

        return $this;
    }

    /**
     * @param Closure(T, array-key): bool $callable
     */
    public function filter(Closure $callable): self
    {
        $this->collection->filter($callable);

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
     */
    public function map(Closure $callable): self
    {
        $this->collection->map($callable);

        return $this;
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
    public function removeAt(int|string $index): self
    {
        $this->collection->removeAt($index);

        return $this;
    }

    public function values(): self
    {
        $this->collection->values();

        return $this;
    }
}