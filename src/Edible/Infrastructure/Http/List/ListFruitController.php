<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\List;

use App\Edible\Domain\Fruit\FruitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class ListFruitController extends AbstractController
{
    public function __construct(
        private readonly FruitRepository $fruitRepository,
    ) {}

    #[Route('fruits', name: 'list_fruits', methods: ['GET'])]
    public function __invoke(
        #[MapQueryString(serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
        ?ListEdibleRequestDto $listRequestDto
    ): JsonResponse {
        return $this->json(
            $this->fruitRepository->search($listRequestDto?->specification()),
        );
    }
}