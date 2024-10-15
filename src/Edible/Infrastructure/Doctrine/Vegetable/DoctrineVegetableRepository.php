<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Doctrine\Vegetable;

use App\Edible\Domain\EdibleSpecification;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableCollection;
use App\Edible\Domain\Vegetable\VegetableRepository;
use App\Shared\Infrastructure\Doctrine\DoctrineCriteriaConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @extends EntityRepository<Vegetable>
 */
final class DoctrineVegetableRepository extends EntityRepository implements VegetableRepository
{
    public function __construct(
        private readonly DoctrineCriteriaConverter $doctrineCriteriaConverter,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager, new ClassMetadata(Vegetable::class));
    }

    public function search(?EdibleSpecification $specification): VegetableCollection
    {
        $vegetables = is_null($specification)
            ? $this->findAll()
            : $this->matching($this->doctrineCriteriaConverter->convert($specification->criteria()))->toArray();

        return new VegetableCollection(...$vegetables);
    }

    public function get(int $id): ?Vegetable
    {
        return $this->find($id);
    }

    public function save(Vegetable|VegetableCollection $vegetable): void
    {
        $vegetable instanceof Vegetable
            ? $this->getEntityManager()->persist($vegetable)
            : $vegetable->each(fn(Vegetable $vegetable) => $this->getEntityManager()->persist($vegetable));

        $this->getEntityManager()->flush();
    }
}