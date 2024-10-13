<?php

declare(strict_types=1);

namespace App\Edible\Domain;

enum Type: string
{
    case Vegetable = 'vegetable';
    case Fruit = 'fruit';
}