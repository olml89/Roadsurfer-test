<?php

declare(strict_types=1);

namespace App\Shared\Domain\DataProvider;

interface DataProvider
{
    /**
     * @return array<array<string, mixed>>
     */
    public function getData(string $source): array;
}