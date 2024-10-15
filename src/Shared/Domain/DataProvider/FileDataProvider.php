<?php

declare(strict_types=1);

namespace App\Shared\Domain\DataProvider;

use JsonException;
use Throwable;

final class FileDataProvider implements DataProvider
{
    /**
     * @return array<array<string, mixed>>
     * @throws UnreachableDataException
     */
    public function getData(string $source): array
    {
        try {
            $fileContents = @file_get_contents($source);

            if ($fileContents === false) {
                throw UnreachableDataException::invalidSource($source);
            }

            $decoded = json_decode($fileContents, associative: true, flags: JSON_THROW_ON_ERROR);

            if (!is_array($decoded)) {
                throw UnreachableDataException::invalidDataFeed();
            }

            return $decoded;
        }
        catch (UnreachableDataException $e) {
            throw $e;
        }
        catch (JsonException $e) {
            throw UnreachableDataException::invalidJson($e);
        }
        catch (Throwable) {
            throw UnreachableDataException::invalidSource($source);
        }
    }
}