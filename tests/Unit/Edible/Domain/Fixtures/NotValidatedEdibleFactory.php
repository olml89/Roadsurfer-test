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

final class NotValidatedEdibleFactory implements EdibleFactory
{
    /**
     * @param array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $data
     */
    public function create(array $data): Edible
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
}