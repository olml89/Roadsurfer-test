<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Fixtures;

use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Edible\Domain\Vegetable\VegetableRepository;

final class InMemoryVegetableRepository implements VegetableRepository
{
    /**
     * @var array<int, Vegetable>
     */
    private array $vegetables;

    public function get(int $id): ?Vegetable
    {
        foreach ($this->vegetables as $vegetable) {
            if ($vegetable->getId() === $id) {
                return $vegetable;
            }
        }

        return null;
    }

    public function save(Vegetable|VegetableCollection $vegetable): void
    {
        $vegetable instanceof Vegetable
            ? $this->vegetables[] = $vegetable
            : $vegetable->each(
                function (Vegetable $vegetable): void {
                    $this->vegetables[] = $vegetable;
                }
            );
    }
}