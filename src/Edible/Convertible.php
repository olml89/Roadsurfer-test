<?php

declare(strict_types=1);

namespace App\Edible;

use App\Shared\Domain\Unit;

interface Convertible
{
    public function convertTo(Unit $unit): self;
}