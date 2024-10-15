<?php

declare(strict_types=1);

namespace App\Edible\Domain;

final readonly class Quantity
{
    public int $amount;
    public Unit $unit;

    public function __construct(int|float $amount, Unit $unit)
    {
        $this->amount = intval($amount * $unit->multiplierTo($unit->lower()));
        $this->unit = $unit->lower();
    }

    public function format(?Unit $convertTo = null): string
    {
        return sprintf(
            '%s %s',
            $this->amount * $this->unit->multiplierTo($convertTo ?? $this->unit),
            $convertTo?->value ?? $this->unit->value,
        );
    }
}