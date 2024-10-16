<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http;

use App\Edible\Domain\Unit;
use Symfony\Component\Validator\Constraints as Assert;

readonly class DecidesReturnedUnitsDto
{
    #[Assert\Choice([
        Unit::g,
        Unit::kg,
    ])]
    public ?Unit $unit;

    public function __construct(?Unit $unit = null)
    {
        $this->unit = $unit;
    }
}