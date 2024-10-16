<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Specification;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Specification\NameContains;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NameContainsTest extends TestCase
{
    /**
     * @return array<int, array{0: Edible, 1: NameContains, 2: bool}>>
     */
    public static function provideEdibleSpecificationAndExpectedResult(): array
    {
        return [
            [
                NotValidatedEdibleFactory::generate(
                    name: 'John Smith',
                ),
                new NameContains('J'),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    name: 'John Smith',
                ),
                new NameContains('j'),
                true,
            ],
            [
                NotValidatedEdibleFactory::generate(
                    name: 'John Smith',
                ),
                new NameContains('W'),
                false,
            ],
        ];
    }

    #[DataProvider('provideEdibleSpecificationAndExpectedResult')]
    public function testItIsSatisfiedBy(Edible $edible, NameContains $nameContains, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $nameContains->isSatisfiedBy($edible));
    }
}