<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\List;

use App\Edible\Domain\Vegetable\VegetableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

final class ListVegetableController extends AbstractController
{
    public function __construct(
        private readonly VegetableRepository $vegetableRepository,
    ) {}

    #[Route('vegetables', name: 'list_vegetables', methods: ['GET'])]
    public function __invoke(#[MapQueryString] ?ListEdibleRequestDto $listRequestDto): JsonResponse
    {
        return $this->json(
            $this->vegetableRepository->search($listRequestDto?->specification()),
        );
    }
}