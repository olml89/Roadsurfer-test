<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;

trait ProvidesEdibleCreationData
{
    /**
     * @return array<'id'|'name'|'quantity'|'type'|'unit', mixed>
     */
    protected static function edibleData(
        mixed $id = null,
        mixed $name = null,
        mixed $type = null,
        mixed $quantity = null,
        mixed $unit = null,
    ): array {
        return [
            'id' => $id ?? 1,
            'name' => $name ?? 'name',
            'type' => $type ?? Type::cases()[array_rand(Type::cases())]->value,
            'quantity' => $quantity ?? 20,
            'unit' => $unit ?? Unit::cases()[array_rand(Unit::cases())]->value,
        ];
    }
}