<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Collection;

use App\Shared\Domain\Collection\CollectionWrapper;
use ArrayObject;
use InvalidArgumentException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionWrapperNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    private NormalizerInterface $normalizer;

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>|ArrayObject<string, mixed>|bool|float|int|string|null
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array|ArrayObject|bool|float|int|string|null
    {
        if (!($object instanceof CollectionWrapper)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s must be an instance of "%s"',
                self::class,
                CollectionWrapper::class,
            ));
        }

       return $this->normalizer->normalize($object->list(), $format, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof CollectionWrapper;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CollectionWrapper::class => true,
        ];
    }
}