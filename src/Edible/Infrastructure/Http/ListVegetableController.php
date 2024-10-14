<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http;

use App\Edible\Domain\Vegetable\VegetableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ListVegetableController extends AbstractController
{
    public function __construct(
        private readonly VegetableRepository $vegetableRepository,
    ) {}

    #[Route('vegetables', name: 'list_vegetables')]
    public function __invoke(Request $request): JsonResponse
    {
        return $this->json(
            $this->vegetableRepository->all()->list(),
        );
    }
}