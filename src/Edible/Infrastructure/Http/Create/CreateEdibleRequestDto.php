<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\Create;

use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * We have to avoid this DTO being treated as a service and exclude it from being automatically loaded by the container,
 * or the instantiation falls due to having a nested DTO as a parameter.
 *
 * https://github.com/symfony/symfony/issues/50708
 */
#[Exclude]
final readonly class CreateEdibleRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        #[Assert\Positive]
        public int $id,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Choice([
            Type::Fruit,
            Type::Vegetable,
        ])]
        public Type $type,

        #[Assert\NotBlank]
        #[Assert\Type('numeric')]
        #[Assert\Positive]
        public int $quantity,

        #[Assert\NotBlank]
        #[Assert\Choice([
            Unit::g,
            Unit::kg,
        ])]
        public Unit $unit,
    ) {
    }
}