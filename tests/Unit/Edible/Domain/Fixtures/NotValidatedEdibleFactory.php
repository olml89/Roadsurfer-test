<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Fixtures;

use App\Edible\Domain\Edible;
use App\Edible\Domain\EdibleFactory;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Tests\Helpers\ProvidesEdibleCreationData;

final class NotValidatedEdibleFactory implements EdibleFactory
{
    use ProvidesEdibleCreationData;

    /**
     * @param array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $data
     */
    public function create(array $data): Edible
    {
        return self::createStatic($data);
    }

    /**
     * @param array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $data
     */
    public static function createStatic(array $data): Edible
    {
        $type = Type::from($data['type']);

        return match ($type) {
            Type::Fruit => new Fruit(
                id: $data['id'],
                name: $data['name'],
                quantity: Quantity::create($data['quantity'], Unit::from($data['unit'])),
            ),
            Type::Vegetable => new Vegetable(
                id: $data['id'],
                name: $data['name'],
                quantity: Quantity::create($data['quantity'], Unit::from($data['unit'])),
            ),
        };
    }

    public static function generate(?string $name = null, ?Quantity $quantity = null): Edible
    {
        /** @var array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $edibleData */
        $edibleData = self::edibleData();
        $edible = self::createStatic($edibleData);

        return $edible
            ->setName($name ?? $edible->getName())
            ->setQuantity($quantity ?? $edible->getQuantity());
    }
}