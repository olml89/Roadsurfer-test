<?php

declare(strict_types=1);

namespace App\Edible\Domain;

use UnexpectedValueException;

interface EdibleFactory
{
    /**
     * @param array<string, mixed> $data
     * @throws UnexpectedValueException
     */
    public function create(array $data): Edible;
}