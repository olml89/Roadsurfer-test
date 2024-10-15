<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use App\Shared\Domain\Criteria\Specification;

interface EdibleSpecification extends Specification
{
    public function isSatisfiedBy(Edible $edible): bool;
}