<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Fixtures;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Fruit\FruitRepository;

final class InMemoryFruitRepository implements FruitRepository
{
    /**
     * @var array<int, Fruit>
     */
    private array $fruits;

    public function get(int $id): ?Fruit
    {
        foreach ($this->fruits as $fruit) {
            if ($fruit->getId() === $id) {
                return $fruit;
            }
        }

        return null;
    }

    public function save(FruitCollection|Fruit $fruit): void
    {
        $fruit instanceof Fruit
            ? $this->fruits[] = $fruit
            : $fruit->each(
                function (Fruit $fruit): void {
                    $this->fruits[] = $fruit;
                }
            );
    }
}