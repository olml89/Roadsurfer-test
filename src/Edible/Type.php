<?php

declare(strict_types=1);

namespace App\Edible;

enum Type: string
{
    case Vegetable = 'vegetable';
    case Fruit = 'fruit';
}