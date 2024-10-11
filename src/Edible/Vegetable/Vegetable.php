<?php

declare(strict_types=1);

namespace App\Edible\Vegetable;

use App\Edible\Edible;
use App\Edible\Quantity;
use App\Edible\Type;

final class Vegetable extends Edible
{
    public function __construct(string $name, Quantity $quantity)
    {
        parent::__construct(
            name: $name,
            type: Type::Vegetable,
            quantity: $quantity,
        );
    }
}