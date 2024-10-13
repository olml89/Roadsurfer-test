<?php

declare(strict_types=1);

namespace App\Edible\Domain;

interface Convertible
{
    public function convertTo(Unit $unit): self;
}