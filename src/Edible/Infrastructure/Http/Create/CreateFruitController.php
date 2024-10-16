<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\Create;

use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Fruit\FruitRepository;
use App\Edible\Domain\Quantity;
use App\Edible\Infrastructure\Http\DecidesReturnedUnitsDto;
use App\Edible\Infrastructure\Http\UnitsConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class CreateFruitController extends AbstractController
{
    public function __construct(
        private readonly FruitRepository $fruitRepository,
        private readonly UnitsConverter $unitsConverter,
    ) {}

    #[Route('fruits', name: 'create_fruit', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload(serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
        CreateEdibleRequestDto $createRequestDto,

        #[MapQueryString(serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
        ?DecidesReturnedUnitsDto $decideReturnedUnitsDto,
    ): JsonResponse {
        $fruit = new Fruit(
            id: $createRequestDto->id,
            name: $createRequestDto->name,
            quantity: Quantity::create(amount: $createRequestDto->quantity, unit: $createRequestDto->unit),
        );

        $this->fruitRepository->save($fruit);
        $this->unitsConverter->convert($fruit, $decideReturnedUnitsDto?->unit);

        return $this->json($fruit, status: Response::HTTP_CREATED);
    }
}