<?php

declare(strict_types=1);

namespace App\Shared\Domain;

trait EnumValues
{
    /**
     * @return array<int, value-of<self>>
     */
    public static function values(): array
    {
        return array_map(
            fn(self $unit) => $unit->value,
            self::cases(),
        );
    }
}
