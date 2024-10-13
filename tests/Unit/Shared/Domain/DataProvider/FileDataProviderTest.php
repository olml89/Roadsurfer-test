<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\DataProvider;

use App\Shared\Domain\DataProvider\FileDataProvider;
use App\Shared\Domain\DataProvider\UnreachableDataException;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileDataProvider::class)]
#[UsesClass(UnreachableDataException::class)]
final class FileDataProviderTest extends TestCase
{
    private const string FIXTURES = __DIR__ . '/Fixtures';

    private FileDataProvider $fileDataProvider;

    protected function setUp(): void
    {
        $this->fileDataProvider = new FileDataProvider();
    }

    public function testItThrowsExceptionIfFileDoesNotExist(): void
    {
        $unexistingFile = 'unexisting_file.json';

        $this->expectExceptionObject(
            UnreachableDataException::invalidSource($unexistingFile),
        );

        $this->fileDataProvider->getData($unexistingFile);
    }

    public function testItThrowsExceptionIfJsonIsInvalid(): void
    {
        $this->expectExceptionObject(
            UnreachableDataException::invalidJson(new JsonException('Syntax error')),
        );

        $this->fileDataProvider->getData(self::FIXTURES . '/invalid_json.json');
    }

    public function testItThrowsExceptionIfFileIsNotAValidFeed(): void
    {
        $this->expectExceptionObject(
            UnreachableDataException::invalidDataFeed(),
        );

        $this->fileDataProvider->getData(self::FIXTURES . '/invalid_feed.json');
    }

    public function testItParsesValidFeedJsonFile(): void
    {
        $validFeed = $this->fileDataProvider->getData('/app/request.json');

        $this->assertCount(20, $validFeed);
    }
}