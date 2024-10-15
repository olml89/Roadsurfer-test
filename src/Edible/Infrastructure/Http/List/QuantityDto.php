<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\List;

use App\Edible\Domain\Unit;
use App\Shared\Domain\Criteria\Filter\Operator;
use Symfony\Component\Validator\Constraints as Assert;

final class QuantityDto
{
    public function __construct(
        #[Assert\AtLeastOneOf([
            new Assert\Type('numeric'),
            new Assert\Type('string'),
        ])]
        public null|int|float|string $amount,

        #[Assert\Choice([
            Operator::LTE,
            Operator::LT,
            Operator::EQ,
            Operator::NEQ,
            Operator::GTE,
            Operator::GT,
            Operator::IN,
            Operator::NIN,
            Operator::LIKE,
        ])]
        public Operator $op = Operator::EQ,

        #[Assert\Choice([
            Unit::g,
            Unit::kg,
        ])]
        public Unit $unit = Unit::g,
    ) {
    }
}