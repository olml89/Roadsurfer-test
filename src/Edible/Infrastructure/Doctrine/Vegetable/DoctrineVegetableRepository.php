<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Doctrine\Vegetable;

use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Edible\Domain\Vegetable\VegetableRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineVegetableRepository implements VegetableRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function all(): VegetableCollection
    {
        return new VegetableCollection(
            ...$this->entityManager->getRepository(Vegetable::class)->findAll()
        );
    }

    public function get(int $id): ?Vegetable
    {
        return $this->entityManager->getRepository(Vegetable::class)->find($id);
    }

    public function save(Vegetable|VegetableCollection $vegetable): void
    {
        $vegetable instanceof Vegetable
            ? $this->entityManager->persist($vegetable)
            : $vegetable->each(fn(Vegetable $vegetable) => $this->entityManager->persist($vegetable));

        $this->entityManager->flush();
    }
}