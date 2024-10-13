<?php

declare(strict_types=1);

namespace App\Shared\Domain\DataProvider;

use JsonException;
use RuntimeException;
use Throwable;

final class UnreachableDataException extends RuntimeException
{
    private function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function invalidSource(string $source): self
    {
        return new self(sprintf('No data source found at "%s".', $source));
    }

    public static function invalidJson(JsonException $e): self
    {
        return new self(sprintf('Error while parsing JSON: %s', $e->getMessage()), previous: $e);
    }

    public static function invalidDataFeed(): self
    {
        return new self('Invalid data feed.');
    }
}