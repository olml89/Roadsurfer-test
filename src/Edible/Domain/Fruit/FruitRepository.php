<?php

declare(strict_types=1);

namespace App\Edible\Domain\Fruit;

use App\Edible\Domain\EdibleSpecification;

interface FruitRepository
{
    public function search(?EdibleSpecification $specification): FruitCollection;
    public function get(int $id): ?Fruit;
    public function save(Fruit|FruitCollection $fruit): void;
}