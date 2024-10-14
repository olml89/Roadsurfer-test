<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Fixtures;

use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Edible\Domain\Vegetable\VegetableRepository;

final class InMemoryVegetableRepository implements VegetableRepository
{
    private VegetableCollection $vegetables;

    public function __construct()
    {
        $this->vegetables = new VegetableCollection();
    }

    public function all(): VegetableCollection
    {
        return $this->vegetables;
    }

    public function get(int $id): ?Vegetable
    {
        foreach ($this->vegetables->list() as $vegetable) {
            if ($vegetable->getId() === $id) {
                return $vegetable;
            }
        }

        return null;
    }

    public function save(Vegetable|VegetableCollection $vegetable): void
    {
        $vegetable instanceof Vegetable
            ? $this->vegetables->add($vegetable)
            : $this->vegetables->add(...$vegetable->list());
    }
}