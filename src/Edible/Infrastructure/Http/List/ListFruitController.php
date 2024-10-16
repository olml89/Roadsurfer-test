<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\List;

use App\Edible\Domain\Fruit\FruitRepository;
use App\Edible\Infrastructure\Http\UnitsConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class ListFruitController extends AbstractController
{
    public function __construct(
        private readonly FruitRepository $fruitRepository,
        private readonly UnitsConverter $unitsConverter,
    ) {}

    #[Route('fruits', name: 'list_fruits', methods: ['GET'])]
    public function __invoke(
        #[MapQueryString(
            serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false],
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST),
        ]
        ?ListEdibleRequestDto $listRequestDto,
    ): JsonResponse {
        $fruits = $this->fruitRepository->search($listRequestDto?->specification());
        $this->unitsConverter->convert($fruits, $listRequestDto?->unit);

        return $this->json($fruits);
    }
}