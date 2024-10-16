<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\List;

use App\Edible\Domain\EdibleSpecification;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Specification\EdibleAndSpecification;
use App\Edible\Domain\Specification\EdibleOrSpecification;
use App\Edible\Domain\Specification\NameContains;
use App\Edible\Domain\Specification\QuantityComparesTo;
use App\Edible\Domain\Unit;
use App\Edible\Infrastructure\Http\DecidesReturnedUnitsDto;
use App\Shared\Domain\Criteria\CompositeExpression\Type;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * We have to avoid this DTO being treated as a service and exclude it from being automatically loaded by the container,
 * or the instantiation falls due to having a nested DTO as a parameter.
 *
 * https://github.com/symfony/symfony/issues/50708
 */
#[Exclude]
final readonly class ListEdibleRequestDto extends DecidesReturnedUnitsDto
{
    public function __construct(
        #[Assert\Length(min: 1, max: 255)]
        public ?string $name = null,

        #[Assert\Choice([
            Type::AND,
            Type::OR,
        ])]
        public Type $op = Type::AND,

        #[Assert\Valid]
        public ?QuantityDto $quantity = null,

        ?Unit $unit = null,
    ) {
        parent::__construct($unit);
    }

    public function specification(): ?EdibleSpecification
    {
        $specifications = array_filter([
            !is_null($this->name)
                ? new NameContains($this->name)
                : null,
            !is_null($this->quantity?->amount)
                ? new QuantityComparesTo($this->quantity->op, ...$this->buildRequestedQuantities($this->quantity))
                : null,
        ]);

        if (count($specifications) === 0) {
            return null;
        }

        return $this->op === Type::AND
            ? new EdibleAndSpecification(...$specifications)
            : new EdibleOrSpecification(...$specifications);
    }

    /**
     * @return array<int, Quantity>
     */
    private function buildRequestedQuantities(?QuantityDto $quantityDto): array
    {
        if (is_null($quantityDto?->amount)) {
            return [];
        }

        if (!is_string($quantityDto->amount)) {
            return [
                Quantity::create(amount: $quantityDto->amount, unit: $quantityDto->unit),
            ];
        }

        $amounts = array_filter(array_map(
            fn(string $stringAmount): ?float => filter_var($stringAmount, FILTER_VALIDATE_FLOAT)
                ? floatval($stringAmount)
                : null,
            explode(',', $quantityDto->amount),
        ));

        return array_map(
            fn(float $amount): Quantity => Quantity::create($amount, $quantityDto->unit),
            $amounts,
        );
    }
}