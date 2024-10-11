<?php

declare(strict_types=1);

namespace App\Edible\Fruit;

use App\Edible\Edible;
use App\Edible\Quantity;
use App\Edible\Type;

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