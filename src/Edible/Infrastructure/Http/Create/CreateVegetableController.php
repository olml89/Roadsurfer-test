<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http\Create;

use App\Edible\Domain\Quantity;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Edible\Domain\Vegetable\VegetableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class CreateVegetableController extends AbstractController
{
    public function __construct(
        private readonly VegetableRepository $vegetableRepository,
    ) {}

    #[Route('vegetables', name: 'create_vegetable', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload(serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
        CreateEdibleRequestDto $createRequestDto
    ): JsonResponse {
        $vegetable = new Vegetable(
            id: $createRequestDto->id,
            name: $createRequestDto->name,
            quantity: new Quantity(amount: $createRequestDto->quantity, unit: $createRequestDto->unit),
        );

        $this->vegetableRepository->save($vegetable);

        return $this->json($vegetable, status: Response::HTTP_CREATED);
    }
}