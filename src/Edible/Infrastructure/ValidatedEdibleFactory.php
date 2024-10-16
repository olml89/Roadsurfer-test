<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\EdibleFactory;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Infrastructure\Http\Create\CreateEdibleRequestDto;
use App\Shared\Domain\ValidationException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

final readonly class ValidatedEdibleFactory implements EdibleFactory
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function create(array $data): Edible
    {
        try {
            /** @var CreateEdibleRequestDto $edibleDto */
            $edibleDto = $this->denormalizer->denormalize(
                data: $data,
                type: CreateEdibleRequestDto::class,
                context: [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ],
            );

            $errors = $this->validator->validate($edibleDto);

            if (count($errors) > 0) {
                throw new ValidationException(
                    message: (string)$errors,
                    previous: new ValidationFailedException(
                        value: $data,
                        violations: $errors,
                    ),
                );
            }

            return match ($edibleDto->type) {
                Type::Fruit => new Fruit(
                    id: $edibleDto->id,
                    name: $edibleDto->name,
                    quantity: Quantity::create(amount: $edibleDto->quantity, unit: $edibleDto->unit),
                ),
                Type::Vegetable => new Vegetable(
                    id: $edibleDto->id,
                    name: $edibleDto->name,
                    quantity: Quantity::create(amount: $edibleDto->quantity, unit: $edibleDto->unit),
                ),
            };
        }
        catch (PartialDenormalizationException $e) {
            $violations = new ConstraintViolationList();

            foreach ($e->getErrors() as $error) {
                $violations->add($this->formatNotNormalizableValueException($error));
            }

            throw new ValidationException(
                message: implode(PHP_EOL, array_map(
                    fn(ConstraintViolationInterface $violation): string => (string)$violation->getMessage(),
                    iterator_to_array($violations),
                )),
                previous: new ValidationFailedException(value: $data, violations: $violations)
            );
        }
        catch (NotNormalizableValueException $e) {
            $violations = new ConstraintViolationList();
            $violations->add($this->formatNotNormalizableValueException($e));

            throw new ValidationException(
                message: $e->getMessage(),
                previous: new ValidationFailedException(value: $data, violations: $violations),
            );
        }
        catch (ValidationException $e) {
            throw $e;
        }
        catch (Throwable $e) {
            throw new ValidationException(
                message: $e->getMessage(),
                previous: $e,
            );
        }
    }

    private function formatNotNormalizableValueException(NotNormalizableValueException $e): ConstraintViolation
    {
        $parameters = [];
        $template = 'This value was of an unexpected type.';

        if ($expectedTypes = $e->getExpectedTypes()) {
            $template = 'This value should be of type {{ type }}.';
            $parameters['{{ type }}'] = implode('|', $expectedTypes);
        }

        return new ConstraintViolation(
            message: $template,
            messageTemplate: $template,
            parameters: $parameters,
            root: null,
            propertyPath: $e->getPath(),
            invalidValue: null,
        );
    }
}