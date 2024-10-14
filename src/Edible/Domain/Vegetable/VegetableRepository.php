<?php

declare(strict_types=1);

namespace App\Edible\Domain\Vegetable;

interface VegetableRepository
{
    public function all(): VegetableCollection;
    public function get(int $id): ?Vegetable;
    public function save(Vegetable|VegetableCollection $vegetable): void;
}