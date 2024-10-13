<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Edible\Domain\EdibleFactory;

final class NotValidatedEdibleFactory implements EdibleFactory
{
    /**
     * @param array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $data
     */
    public function create(array $data): Edible
    {
        return Edible::from(
            id: $data['id'],
            type: Type::from($data['type']),
            name: $data['name'],
            quantity: new Quantity($data['quantity'], Unit::from($data['unit'])),
        );
    }
}