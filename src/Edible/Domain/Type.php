<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use App\Shared\Domain\EnumValues;

enum Type: string
{
    use EnumValues;

    case Vegetable = 'vegetable';
    case Fruit = 'fruit';
}