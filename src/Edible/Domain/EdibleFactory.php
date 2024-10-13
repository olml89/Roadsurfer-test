<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use App\Shared\Domain\Validation\ValidationException;

interface EdibleFactory
{
    /**
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function create(array $data): Edible;
}