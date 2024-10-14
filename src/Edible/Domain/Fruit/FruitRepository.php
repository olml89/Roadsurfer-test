<?php

declare(strict_types=1);

namespace App\Edible\Domain\Fruit;

interface FruitRepository
{
    public function all(): FruitCollection;
    public function get(int $id): ?Fruit;
    public function save(Fruit|FruitCollection $fruit): void;
}