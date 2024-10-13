<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Vegetable\Vegetable;

abstract class Edible
{
    protected ?int $id = null;
    protected string $name;
    protected Type $type;
    protected Quantity $quantity;

    public function __construct(string $name, Type $type, Quantity $quantity)
    {
        $this->name = $name;
        $this->type = $type;
        $this->quantity = $quantity;
    }

    public static function from(Type $type, string $name, Quantity $quantity): self
    {
        return match ($type) {
            Type::Fruit => new Fruit($name, $quantity),
            Type::Vegetable => new Vegetable($name, $quantity),
        };
    }

    public function getId(): ?int
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