<?php

declare(strict_types=1);

namespace App\Edible\Domain\Fruit;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;

final class Fruit extends Edible
{
    public function __construct(string $name, Quantity $quantity)
    {
        parent::__construct(
            name: $name,
            type: Type::Fruit,
            quantity: $quantity,
        );
    }
}