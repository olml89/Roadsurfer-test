<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Fixtures;

use App\Edible\Domain\EdibleSpecification;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Fruit\FruitRepository;

final class InMemoryFruitRepository implements FruitRepository
{
    private FruitCollection $fruits;

    public function __construct()
    {
        $this->fruits = new FruitCollection();
    }

    public function search(?EdibleSpecification $specification): FruitCollection
    {
        return is_null($specification)
            ? $this->fruits
            : $this->fruits->filter(
                fn(Fruit $fruit): bool => $specification->isSatisfiedBy($fruit)
            );
    }

    public function get(int $id): ?Fruit
    {
        foreach ($this->fruits->list() as $fruit) {
            if ($fruit->getId() === $id) {
                return $fruit;
            }
        }

        return null;
    }

    public function save(FruitCollection|Fruit $fruit): void
    {
        $fruit instanceof Fruit
            ? $this->fruits->add($fruit)
            : $this->fruits->add(...$fruit->list());
    }
}