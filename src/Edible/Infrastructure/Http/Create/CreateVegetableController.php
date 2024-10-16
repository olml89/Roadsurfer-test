<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\Create;

use App\Edible\Domain\Quantity;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableRepository;
use App\Edible\Infrastructure\Http\DecidesReturnedUnitsDto;
use App\Edible\Infrastructure\Http\UnitsConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class CreateVegetableController extends AbstractController
{
    public function __construct(
        private readonly VegetableRepository $vegetableRepository,
        private readonly UnitsConverter $unitsConverter,
    ) {}

    #[Route('vegetables', name: 'create_vegetable', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload(serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
        CreateEdibleRequestDto $createRequestDto,

        #[MapQueryString(serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
        ?DecidesReturnedUnitsDto $decideReturnedUnitsDto,
    ): JsonResponse {
        $vegetable = new Vegetable(
            id: $createRequestDto->id,
            name: $createRequestDto->name,
            quantity: Quantity::create(amount: $createRequestDto->quantity, unit: $createRequestDto->unit),
        );

        $this->vegetableRepository->save($vegetable);
        $this->unitsConverter->convert($vegetable, $decideReturnedUnitsDto?->unit);

        return $this->json($vegetable, status: Response::HTTP_CREATED);
    }
}