<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\EdibleFactory;
use App\Shared\Domain\Validation\ValidationError;
use App\Shared\Domain\Validation\ValidationException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ValidatedEdibleFactory implements EdibleFactory
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function create(array $data): Edible
    {
        $constraints = new Assert\Collection([
            'id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive(),
            ],
            'name' => new Assert\NotBlank(),
            'type' => [
                new Assert\NotBlank(),
                new Assert\Choice(Type::values()),
            ],
            'quantity' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\Positive(),
            ],
            'unit' => [
                new Assert\NotBlank(),
                new Assert\Choice(Unit::values()),
            ],
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if (count($errors) > 0) {
            throw $this->createValidationException($errors);
        }

        /** @var array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $data */
        return Edible::from(
            id: $data['id'],
            type: Type::from($data['type']),
            name: $data['name'],
            quantity: new Quantity($data['quantity'], Unit::from($data['unit'])),
        );
    }

    private function createValidationException(ConstraintViolationListInterface $violations): ValidationException
    {
        $validationErrors = [];

        foreach ($violations as $violation) {
            $validationErrors[] = new ValidationError(
                property: $violation->getPropertyPath(),
                message: (string)$violation->getMessage(),
            );
        }

        return new ValidationException((string)$violations, ...$validationErrors);
    }
}