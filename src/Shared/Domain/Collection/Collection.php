<?php

declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use Closure;
use Countable;

/**
 * @template T
 */
final class Collection implements Countable
{
    /**
     * @var array<array-key, T>
     */
    private array $items;

    /**
     * @param array<array-key, T> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param T ...$items
     * @return Collection<T>
     */
    public function add(mixed ...$items): self
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * @return Collection<T>
     */
    public function clear(): self
    {
        $this->items = [];

        return $this;
    }

    public function contains(mixed $item): bool
    {
        return in_array($item, $this->items, strict: true);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param Closure(T): void $callback
     * @return Collection<T>
     */
    public function each(Closure $callback): self
    {
        foreach ($this->items as $item) {
            $callback($item);
        }

        return $this;
    }

    /**
     * @param Closure(T, array-key): bool $callable
     * @return Collection<T>
     */
    public function filter(Closure $callable): self
    {
        return new self(
            array_values(array_filter($this->items, $callable, mode: ARRAY_FILTER_USE_BOTH))
        );
    }

    /**
     * @param array-key $index
     * @return ?T
     */
    public function get(int|string $index): mixed
    {
        return $this->items[$index] ?? null;
    }

    /**
     * @param T $item
     * @return array-key|false
     */
    public function indexOf(mixed $item): int|string|false
    {
        return array_search($item, $this->items, strict: true);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return array<array-key, T>
     */
    public function list(): array
    {
        return $this->items;
    }

    /**
     * @param Closure(T): mixed $callable
     * @return Collection<mixed>
     */
    public function map(Closure $callable): self
    {
        return new self(
            array_map($callable, $this->items)
        );
    }

    /**
     * @param T ...$items
     * @return Collection<T>
     */
    public function prepend(mixed ...$items): self
    {
        $this->items = [
            ...$items,
            ...$this->items,
        ];

        return $this;
    }

    /**
     * @template R
     * @param Closure(R, T): R $callable
     * @return R
     */
    public function reduce(Closure $callable, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callable, initial: $initial);
    }

    /**
     * @param T $item
     * @return Collection<T>
     */
    public function remove(mixed $item): self
    {
        $index = $this->indexOf($item);

        if ($index === false) {
            return $this;
        }

        return $this->removeAt($index);
    }

    /**
     * @param array-key $index
     * @return Collection<T>
     */
    public function removeAt(int|string $index): self
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        return $this;
    }

    /**
     * @param Closure(T): mixed $callable
     * @return Collection<mixed>
     */
    public function transform(Closure $callable): self
    {
        $this->items = array_map($callable, $this->items);

        return $this;
    }

    /**
     * @return Collection<T>
     */
    public function values(): self
    {
        return new self(array_values($this->items));
    }
}