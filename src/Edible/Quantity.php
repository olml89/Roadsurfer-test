<?php

declare(strict_types=1);

namespace App\Edible;

use App\Shared\Domain\Unit;

final readonly class Quantity implements Convertible
{
    public function __construct(
        public float $amount,
        public Unit $unit,
    ) {
    }

    public function convertTo(Unit $unit): self
    {
        return new self($this->amount * $this->unit->multiplierTo($unit), $unit);
    }
}