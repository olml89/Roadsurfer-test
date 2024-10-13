<?php

declare(strict_types=1);

namespace App\Shared\Domain\Validation;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    /**
     * @var array<int|string, ValidationError>
     */
    private readonly array $validationErrors;

    public function __construct(string $message, ValidationError ...$validationErrors)
    {
        $this->validationErrors = $validationErrors;

        parent::__construct($message);
    }

    /**
     * @return array<int|string, ValidationError>
     */
    public function validationErrors(): array
    {
        return $this->validationErrors;
    }
}