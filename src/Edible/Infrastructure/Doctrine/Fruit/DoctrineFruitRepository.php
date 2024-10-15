<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Doctrine\Fruit;

use App\Edible\Domain\EdibleSpecification;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitCollection;
use App\Edible\Domain\Fruit\FruitRepository;
use App\Shared\Infrastructure\Doctrine\DoctrineCriteriaConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @extends EntityRepository<Fruit>
 */
final class DoctrineFruitRepository extends EntityRepository implements FruitRepository
{
    public function __construct(
        private readonly DoctrineCriteriaConverter $doctrineCriteriaConverter,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager, new ClassMetadata(Fruit::class));
    }

    public function search(?EdibleSpecification $specification): FruitCollection
    {
        $fruits = is_null($specification)
            ? $this->findAll()
            : $this->matching($this->doctrineCriteriaConverter->convert($specification->criteria()))->toArray();

        return new FruitCollection(...$fruits);
    }

    public function get(int $id): ?Fruit
    {
        return $this->find($id);
    }

    public function save(FruitCollection|Fruit $fruit): void
    {
        $fruit instanceof Fruit
            ? $this->getEntityManager()->persist($fruit)
            : $fruit->each(fn(Fruit $fruit) => $this->getEntityManager()->persist($fruit));

        $this->getEntityManager()->flush();
    }
}