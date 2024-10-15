<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\Filter;

final readonly class LessThan extends Filter
{
    public function __construct(string $field, mixed $value)
    {
        parent::__construct(
            field: $field,
            operator: Operator::LT,
            value: $value
        );
    }
}
