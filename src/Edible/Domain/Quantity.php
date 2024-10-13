<?php

declare(strict_types=1);

namespace App\Edible\Domain;

final readonly class Quantity implements Convertible
{
    public function __construct(
        public float $amount,
        public Unit $unit,
    ) {
    }

    public function convertTo(Unit $unit): self
    {
        return new self(amount: $this->amount * $this->unit->multiplierTo($unit), unit: $unit);
    }
}