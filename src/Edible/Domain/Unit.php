<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use App\Shared\Domain\EnumValues;

enum Unit: string
{
    use EnumValues;

    case kg = 'kg';
    case g = 'g';

    private const array CONVERSIONS = [
        self::kg->name => [
            self::kg->name => 1,
            self::g->name => 1000,
        ],
        self::g->name => [
            self::g->name => 1,
            self::kg->name => 0.001,
        ],
    ];

    public function multiplierTo(Unit $unit): float
    {
        return self::CONVERSIONS[$this->name][$unit->name];
    }
}
