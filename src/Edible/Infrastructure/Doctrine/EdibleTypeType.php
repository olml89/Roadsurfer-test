<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Doctrine;

use App\Edible\Domain\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\StringType;
use Throwable;

final class EdibleTypeType extends StringType
{
    private const string NAME = 'edibleType';

    public static function getTypeName(): string
    {
        return self::NAME;
    }

    /**
     * @throws InvalidType
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (!($value instanceof Type)) {
            throw InvalidType::new(
                value: $value,
                toType: self::class,
                possibleTypes: [
                    Type::class,
                ],
            );
        }

        return $value->value;
    }

    /**
     * @throws ValueNotConvertible
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Type
    {
        if (!is_string($value)) {
            throw ValueNotConvertible::new(
                value: $value,
                toType: self::class,
            );
        }

        try {
            return Type::from($value);
        }
        catch (Throwable $e) {
            throw ValueNotConvertible::new(
                value: $value,
                toType: self::class,
                previous: $e,
            );
        }
    }
}