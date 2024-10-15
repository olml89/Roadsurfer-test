<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use RuntimeException;
use Throwable;

final class ValidationException extends RuntimeException
{
    public function __construct(string $message, Throwable $previous)
    {
        parent::__construct(message: $message, previous: $previous);
    }
}