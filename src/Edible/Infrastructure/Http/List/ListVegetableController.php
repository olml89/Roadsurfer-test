<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\List;

use App\Edible\Domain\Vegetable\VegetableRepository;
use App\Edible\Infrastructure\Http\UnitsConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class ListVegetableController extends AbstractController
{
    public function __construct(
        private readonly VegetableRepository $vegetableRepository,
        private readonly UnitsConverter $unitsConverter,
    ) {}

    #[Route('vegetables', name: 'list_vegetables', methods: ['GET'])]
    public function __invoke(
        #[MapQueryString(
            serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false],
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST),
        ]
        ?ListEdibleRequestDto $listRequestDto,
    ): JsonResponse {
        $vegetables = $this->vegetableRepository->search($listRequestDto?->specification());
        $this->unitsConverter->convert($vegetables, $listRequestDto?->unit);

        return $this->json($vegetables);
    }
}