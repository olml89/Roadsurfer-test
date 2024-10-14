<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Infrastructure;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Infrastructure\ValidatedEdibleFactory;
use App\Shared\Domain\Validation\ValidationError;
use App\Shared\Domain\Validation\ValidationException;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(ValidatedEdibleFactory::class)]
#[UsesClass(Type::class)]
#[UsesClass(Unit::class)]
#[UsesClass(Quantity::class)]
#[UsesClass(Edible::class)]
#[UsesClass(Fruit::class)]
#[UsesClass(Vegetable::class)]
#[UsesClass(ValidationException::class)]
#[UsesClass(ValidationError::class)]
final class ValidatedEdibleFactoryTest extends KernelTestCase
{
    private ValidatedEdibleFactory $factory;

    protected function setUp(): void
    {
        $this->factory = $this->get(ValidatedEdibleFactory::class);
    }

    /**
     * @return array<'id'|'name'|'quantity'|'type'|'unit', mixed>
     */
    private static function createEdibleData(
        mixed $id = null,
        mixed $name = null,
        mixed $type = null,
        mixed $quantity = null,
        mixed $unit = null,
    ): array {
        return [
            'id' => $id ?? 1,
            'name' => $name ?? 'name',
            'type' => $type ?? Type::cases()[array_rand(Type::cases())]->value,
            'quantity' => $quantity ?? 20,
            'unit' => $unit ?? Unit::cases()[array_rand(Unit::cases())]->value,
        ];
    }

    /**
     * @return array<string, array{array<'id'|'name'|'quantity'|'type'|'unit'|'extra_field', mixed>, ValidationError}>
     */
    public static function provideInvalidInputAndExpectedException(): array
    {
        return [
            'id is missing' => [
                array_diff_key(
                    self::createEdibleData(),
                    array_flip(['id']),
                ),
                new ValidationError(
                    property: '[id]',
                    message: 'This field is missing.',
                ),
            ],
            'id is blank' => [
                self::createEdibleData(id: ''),
                new ValidationError(
                    property: '[id]',
                    message: 'This value should not be blank.',
                ),
            ],
            'id is not an integer' => [
                self::createEdibleData(id: 3.1416),
                new ValidationError(
                    property: '[id]',
                    message: 'This value should be of type integer.',
                ),
            ],
            'id is not positive' => [
                self::createEdibleData(id: -1),
                new ValidationError(
                    property: '[id]',
                    message: 'This value should be positive.',
                ),
            ],
            'name is missing' => [
                array_diff_key(
                    self::createEdibleData(),
                    array_flip(['name']),
                ),
                new ValidationError(
                    property: '[name]',
                    message: 'This field is missing.',
                ),
            ],
            'name is blank' => [
                self::createEdibleData(name: ''),
                new ValidationError(
                    property: '[name]',
                    message: 'This value should not be blank.',
                ),
            ],
            'type is missing' => [
                array_diff_key(
                    self::createEdibleData(),
                    array_flip(['type']),
                ),
                new ValidationError(
                    property: '[type]',
                    message: 'This field is missing.',
                ),
            ],
            'type is blank' => [
                self::createEdibleData(type: ''),
                new ValidationError(
                    property: '[type]',
                    message: 'This value should not be blank.',
                ),
            ],
            'type is not valid' => [
                self::createEdibleData(type: 'type'),
                new ValidationError(
                    property: '[type]',
                    message: 'The value you selected is not a valid choice.',
                ),
            ],
            'quantity is missing' => [
                array_diff_key(
                    self::createEdibleData(),
                    array_flip(['quantity']),
                ),
                new ValidationError(
                    property: '[quantity]',
                    message: 'This field is missing.',
                ),
            ],
            'quantity is blank' => [
                self::createEdibleData(quantity: false),
                new ValidationError(
                    property: '[quantity]',
                    message: 'This value should not be blank.',
                ),
            ],
            'quantity is not numeric' => [
                self::createEdibleData(quantity: 'six'),
                new ValidationError(
                    property: '[quantity]',
                    message: 'This value should be of type numeric.',
                ),
            ],
            'quantity is not positive' => [
                self::createEdibleData(quantity: -12.5),
                new ValidationError(
                    property: '[quantity]',
                    message: 'This value should be positive.',
                ),
            ],
            'unit is missing' => [
                array_diff_key(
                    self::createEdibleData(),
                    array_flip(['unit']),
                ),
                new ValidationError(
                    property: '[unit]',
                    message: 'This field is missing.',
                ),
            ],
            'unit is blank' => [
                self::createEdibleData(unit: false),
                new ValidationError(
                    property: '[unit]',
                    message: 'This value should not be blank.',
                ),
            ],
            'unit is not valid' => [
                self::createEdibleData(unit: 'unit'),
                new ValidationError(
                    property: '[unit]',
                    message: 'The value you selected is not a valid choice.',
                ),
            ],
            'extra field' => [
                array_merge(
                    self::createEdibleData(),
                    [
                        'extra_field' => 'random',
                    ],
                ),
                new ValidationError(
                    property: '[extra_field]',
                    message: 'This field was not expected.',
                )
            ],
        ];
    }

    /**
     * @param array<'id'|'name'|'quantity'|'type'|'unit', mixed> $data
     */
    #[DataProvider('provideInvalidInputAndExpectedException')]
    public function testItThrowsUnexpectedValueExceptionOnInvalidInput(array $data, ValidationError $validationError): void
    {
        try {
            $this->factory->create($data);
        }
        catch (ValidationException $e) {
            $this->assertEquals($validationError, $e->validationErrors()[0]);
        }
    }

    public function testItCreatesEdibleOnValidInput(): void
    {
        /** @var array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $edibleData */
        $edibleData = self::createEdibleData();

        $edible = $this->factory->create($edibleData);

        $this->assertEquals($edibleData['id'], $edible->getId());
        $this->assertEquals($edibleData['name'], $edible->getName());

        $this->assertInstanceOf(
            Type::from($edibleData['type']) === Type::Fruit ? Fruit::class : Vegetable::class,
            $edible
        );

        $this->assertEquals(
            new Quantity(amount: $edibleData['quantity'], unit: Unit::from($edibleData['unit'])),
            $edible->getQuantity()
        );
    }
}