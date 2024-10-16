<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Vegetable\Vegetable;

abstract class Edible
{
    protected readonly Type $type;
    protected readonly int $id;
    protected string $name;
    protected Quantity $quantity;

    public function __construct(Type $type, int $id, string $name, Quantity $quantity)
    {
        $this->type = $type;
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function setQuantity(Quantity $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}