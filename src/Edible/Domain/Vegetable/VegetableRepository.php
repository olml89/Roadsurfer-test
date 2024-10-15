<?php

declare(strict_types=1);

namespace App\Edible\Domain\Vegetable;

use App\Edible\Domain\EdibleSpecification;

interface VegetableRepository
{
    public function search(?EdibleSpecification $specification): VegetableCollection;
    public function get(int $id): ?Vegetable;
    public function save(Vegetable|VegetableCollection $vegetable): void;
}