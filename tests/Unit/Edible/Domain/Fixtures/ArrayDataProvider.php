<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Fixtures;

use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Shared\Domain\DataProvider\DataProvider;

final readonly class ArrayDataProvider implements DataProvider
{
    public function __construct(
        /**
         * @var array<array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>}>
         */
        private array $data,
    ) {
    }

    public function getData(string $source): array
    {
        return $this->data;
    }
}