<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\ValidationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Throwable;

final readonly class KernelExceptionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $environment,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof UniqueConstraintViolationException) {
            $event->setResponse(
                new JsonResponse(
                    data: $this->formatResponsePayload('Entity already exists', $exception),
                    status: Response::HTTP_CONFLICT,
                ),
            );

            return;
        }

        if ($exception instanceof ValidationException) {
            $event->setResponse(
                new JsonResponse(
                    data: $this->formatResponsePayload($exception->getMessage(), $exception),
                    status: Response::HTTP_UNPROCESSABLE_ENTITY,
                ),
            );

            return;
        }

        if ($exception instanceof ExtraAttributesException || $exception instanceof InvalidArgumentException) {
            $event->setResponse(
                new JsonResponse(
                    data: $this->formatResponsePayload($exception->getMessage(), $exception),
                    status: Response::HTTP_UNPROCESSABLE_ENTITY,
                ),
            );

            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse(
                new JsonResponse(
                    data: $this->formatResponsePayload($exception->getMessage(), $exception),
                    status: $exception->getStatusCode(),
                    headers: $exception->getHeaders(),
                ),
            );

            return;
        }

        $event->setResponse(
            new JsonResponse(
                data: $this->formatResponsePayload($exception->getMessage(), $exception),
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function formatResponsePayload(string $message, Throwable $e): array
    {
        return array_filter([
            'message' => $message,
            'debug' => $this->environment === 'dev' ? $e->getTrace() : null,
        ]);
    }
}