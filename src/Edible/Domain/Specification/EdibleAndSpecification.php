<?php

declare(strict_types=1);

namespace App\Edible\Domain\Specification;

use App\Edible\Domain\Edible;
use App\Edible\Domain\EdibleSpecification;
use App\Shared\Domain\Criteria\CompositeExpression\AndExpression;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Expression;

final readonly class EdibleAndSpecification implements EdibleSpecification
{
    /**
     * @var array<array-key, EdibleSpecification>
     */
    private array $specifications;

    public function __construct(EdibleSpecification ...$specifications)
    {
        $this->specifications = $specifications;
    }

    public function isSatisfiedBy(Edible $edible): bool
    {
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($edible)) {
                return false;
            }
        }

        return true;
    }

    public function criteria(): Criteria
    {
        return new Criteria(
            expression: new AndExpression(
                ...array_map(
                    fn(EdibleSpecification $specification): Expression => $specification->criteria()->expression,
                    $this->specifications,
                )
            )
        );
    }
}