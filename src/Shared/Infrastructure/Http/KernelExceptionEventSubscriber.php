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

        /**
         * A validation has failed in our implemented Factories or in the RequestPayloadValueResolver trying to fill
         * a MapQueryString or a MapRequestPayload object.
         *
         * If the request is a GET, we have to output a 400 Bad Request.
         * If the request is a POST, we have to output a 422 Unprocessable Entity.
         */
        if (
            $exception instanceof ValidationException
            || $exception instanceof ExtraAttributesException
            || $exception instanceof InvalidArgumentException
        ) {
            $statusCode = $event->getRequest()->getMethod() === 'GET'
                ? Response::HTTP_BAD_REQUEST
                : Response::HTTP_UNPROCESSABLE_ENTITY;

            $event->setResponse(
                new JsonResponse(
                    data: $this->formatResponsePayload($exception->getMessage(), $exception),
                    status: $statusCode,
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