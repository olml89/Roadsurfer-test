<?php

declare(strict_types=1);

namespace App\Edible\Domain;

final readonly class Quantity
{
    private function __construct(
        public int|float $amount,
        public Unit $unit,
    ) {}

    public static function create(int|float $amount, Unit $unit): self
    {
        return new self(
            amount: intval($amount * $unit->multiplierTo($unit->lower())),
            unit: $unit->lower(),
        );
    }

    public function convertTo(Unit $convertTo): self
    {
        $amount = round(num: $this->amount * $this->unit->multiplierTo($convertTo), precision: 3);

        return new self(
            amount: floor($amount) === $amount ? intval($amount) : $amount,
            unit: $convertTo,
        );
    }
}