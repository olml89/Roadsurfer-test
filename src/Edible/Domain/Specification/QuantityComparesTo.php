<?php

declare(strict_types=1);

namespace App\Edible\Domain\Specification;

use App\Edible\Domain\Edible;
use App\Edible\Domain\EdibleSpecification;
use App\Edible\Domain\Quantity;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\Operator;
use App\Shared\Domain\ValidationException;

final readonly class QuantityComparesTo implements EdibleSpecification
{
    /**
     * @var array<array-key, Quantity>
     */
    private array $quantities;

    public function __construct(
        private Operator $operator,
        Quantity ...$quantities,
    ) {
        if (count($quantities) === 0) {
            throw new ValidationException('At least one quantity must be provided.');
        }

        if (count($quantities) > 1 && !$this->operator->comparesMultipleValues()) {
            throw new ValidationException(
                sprintf(
                    'Operator %s cannot compare multiple quantities.',
                    $this->operator->value,
                )
            );
        }

        $this->quantities = $quantities;
    }

    /**
     * Since all the Quantities are converted to the lowest unit when created, all of them will have the same
     * Unit, so no need to compare it, and we can cut it to only compare the amount.
     */
    public function isSatisfiedBy(Edible $edible): bool
    {
        return match ($this->operator) {
            Operator::LIKE, Operator::EQ => $edible->getQuantity()->amount === $this->quantities[0]->amount,
            Operator::NEQ => $edible->getQuantity()->amount !== $this->quantities[0]->amount,
            Operator::GT => $edible->getQuantity()->amount > $this->quantities[0]->amount,
            Operator::GTE => $edible->getQuantity()->amount >= $this->quantities[0]->amount,
            Operator::LT => $edible->getQuantity()->amount < $this->quantities[0]->amount,
            Operator::LTE => $edible->getQuantity()->amount <= $this->quantities[0]->amount,
            Operator::IN => in_array(
                $edible->getQuantity()->amount,
                array_map(fn(Quantity $quantity) => $quantity->amount, $this->quantities),
                strict: true,
            ),
            Operator::NIN => !in_array(
                $edible->getQuantity()->amount,
                array_map(fn(Quantity $quantity) => $quantity->amount, $this->quantities),
                strict: true,
            ),
        };
    }

    public function criteria(): Criteria
    {
        $comparableAmount = in_array($this->operator, [Operator::IN, Operator::NIN])
            ? array_map(
                fn(Quantity $quantity): int|float => $quantity->amount,
                $this->quantities
            )
            : $this->quantities[0]->amount;

        return new Criteria(
            expression: Criteria::buildFilter(
                operator: $this->operator,
                field: 'quantity.amount',
                value: $comparableAmount,
            ),
        );
    }
}