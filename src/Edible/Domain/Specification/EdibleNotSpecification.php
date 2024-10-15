<?php

declare(strict_types=1);

namespace App\Edible\Domain\Specification;

use App\Edible\Domain\Edible;
use App\Edible\Domain\EdibleSpecification;
use App\Shared\Domain\Criteria\CompositeExpression\NotExpression;
use App\Shared\Domain\Criteria\Criteria;

final readonly class EdibleNotSpecification implements EdibleSpecification
{
    public function __construct(
        private EdibleSpecification $specification,
    ) {
    }

    public function isSatisfiedBy(Edible $edible): bool
    {
        return !$this->specification->isSatisfiedBy($edible);
    }

    public function criteria(): Criteria
    {
        return new Criteria(
            expression: new NotExpression($this->specification->criteria()->expression),
        );
    }
}