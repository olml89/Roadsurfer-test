<?php

declare(strict_types=1);

namespace App\Shared\Domain\Validation;

final class ValidationError
{
    public function __construct(
        public string $property,
        public string $message,
    ) {
    }
}