<?php

declare(strict_types=1);

namespace App\Edible\Domain\Specification;

use App\Edible\Domain\Edible;
use App\Edible\Domain\EdibleSpecification;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\Like;

final readonly class NameContains implements EdibleSpecification
{
    public function __construct(
        private string $partialMatch,
    ) {}

    public function isSatisfiedBy(Edible $edible): bool
    {
        // SQL LIKE matching is not case-sensitive, we are matching the same behaviour.
        return str_contains(
            strtolower($edible->getName()),
            strtolower($this->partialMatch)
        );
    }

    public function criteria(): Criteria
    {
        return new Criteria(
            expression: new Like(field: 'name', value: $this->partialMatch),
        );
    }
}