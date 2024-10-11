<?php

declare(strict_types=1);

namespace App\Shared\Domain;

class StorageService
{
    protected string $request = '';

    public function __construct(
        string $request
    )
    {
        $this->request = $request;
    }

    public function getRequest(): string
    {
        return $this->request;
    }
}
