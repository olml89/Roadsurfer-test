<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ListFruitController extends AbstractController
{
    public function __construct(
        private readonly FruitRepository $fruitRepository,
    ) {}

    #[Route('fruits', name: 'list_fruits')]
    public function __invoke(Request $request): JsonResponse
    {
        return $this->json(
            $this->fruitRepository->all()->list(),
        );
    }
}