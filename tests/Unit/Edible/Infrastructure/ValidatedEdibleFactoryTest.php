<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Infrastructure;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Infrastructure\ValidatedEdibleFactory;
use App\Shared\Domain\ValidationException;
use App\Tests\Helpers\ProvidesEdibleCreationData;
use App\Tests\KernelTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\String\ByteString;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final class ValidatedEdibleFactoryTest extends KernelTestCase
{
    use ProvidesEdibleCreationData;
    
    private ValidatedEdibleFactory $factory;

    protected function setUp(): void
    {
        $this->factory = $this->get(ValidatedEdibleFactory::class);
    }
    
    /**
     * @return array<string, array{array<'id'|'name'|'quantity'|'type'|'unit'|'extra_field', mixed>, class-string<Throwable>}>
     */
    public static function provideInvalidInputAndExpectedException(): array
    {
        return [
            'id is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['id']),
                ),
                MissingConstructorArgumentsException::class,
            ],
            'id is blank' => [
                self::edibleData(id: ''),
                ValidationFailedException::class,
            ],
            'id is not an integer' => [
                self::edibleData(id: 3.1416),
                ValidationFailedException::class,
            ],
            'id is not positive' => [
                self::edibleData(id: -1),
                ValidationFailedException::class,
            ],
            'name is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['name']),
                ),
                MissingConstructorArgumentsException::class,
            ],
            'name is blank' => [
                self::edibleData(name: ''),
                ValidationFailedException::class,
            ],
            'name is longer than 255 characters' => [
                self::edibleData(name: ByteString::fromRandom(256)->toString()),
                ValidationFailedException::class,
            ],
            'type is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['type']),
                ),
                MissingConstructorArgumentsException::class,
            ],
            'type is blank' => [
                self::edibleData(type: ''),
                InvalidArgumentException::class,
            ],
            'type is not valid' => [
                self::edibleData(type: 'type'),
                InvalidArgumentException::class,
            ],
            'quantity is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['quantity']),
                ),
                MissingConstructorArgumentsException::class,
            ],
            'quantity is blank' => [
                self::edibleData(quantity: false),
                ValidationFailedException::class,
            ],
            'quantity is not numeric' => [
                self::edibleData(quantity: 'six'),
                ValidationFailedException::class,
            ],
            'quantity is not positive' => [
                self::edibleData(quantity: -12.5),
                ValidationFailedException::class,
            ],
            'unit is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['unit']),
                ),
                MissingConstructorArgumentsException::class,
            ],
            'unit is blank' => [
                self::edibleData(unit: false),
                ValidationFailedException::class,
            ],
            'unit is not valid' => [
                self::edibleData(unit: 'unit'),
                InvalidArgumentException::class,
            ],
            'extra field' => [
                array_merge(
                    self::edibleData(),
                    [
                        'extra_field' => 'random',
                    ],
                ),
                ExtraAttributesException::class,
            ],
        ];
    }

    /**
     * @param array<'id'|'name'|'quantity'|'type'|'unit', mixed> $data
     * @param class-string<Throwable> $expectedPreviousExceptionClass
     */
    #[DataProvider('provideInvalidInputAndExpectedException')]
    public function testItThrowsUnexpectedValueExceptionOnInvalidInput(array $data, string $expectedPreviousExceptionClass): void
    {
        try {
            $this->factory->create($data);
        }
        catch (ValidationException $e) {
            $previous = $e->getPrevious();

            $this->assertInstanceOf(
                $expectedPreviousExceptionClass,
                $previous,
            );
        }
    }

    public function testItCreatesEdibleOnValidInput(): void
    {
        /** @var array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $edibleData */
        $edibleData = self::edibleData();

        $edible = $this->factory->create($edibleData);

        $this->assertEquals($edibleData['id'], $edible->getId());
        $this->assertEquals($edibleData['name'], $edible->getName());

        $this->assertInstanceOf(
            Type::from($edibleData['type']) === Type::Fruit ? Fruit::class : Vegetable::class,
            $edible
        );

        $this->assertEquals(
            Quantity::create(amount: $edibleData['quantity'], unit: Unit::from($edibleData['unit'])),
            $edible->getQuantity()
        );
    }
}