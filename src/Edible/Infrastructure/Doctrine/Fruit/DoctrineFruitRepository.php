<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Doctrine\Fruit;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Fruit\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineFruitRepository implements FruitRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function all(): FruitCollection
    {
        return new FruitCollection(
            ...$this->entityManager->getRepository(Fruit::class)->findAll()
        );
    }

    public function get(int $id): ?Fruit
    {
        return $this->entityManager->getRepository(Fruit::class)->find($id);
    }

    public function save(FruitCollection|Fruit $fruit): void
    {
        $fruit instanceof Fruit
            ? $this->entityManager->persist($fruit)
            : $fruit->each(fn(Fruit $fruit) => $this->entityManager->persist($fruit));

        $this->entityManager->flush();
    }
}