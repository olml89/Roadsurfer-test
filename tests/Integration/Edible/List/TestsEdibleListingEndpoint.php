<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\List;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Fruit\Fruit;
use App\Edible\Domain\Vegetable\Vegetable;
use App\Shared\Domain\Collection\CollectionWrapper;
use App\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\ByteString;

/**
 * @mixin KernelTestCase
 */
trait TestsEdibleListingEndpoint
{
    private KernelBrowser $client;
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->client = new KernelBrowser(self::bootKernel());
        $this->serializer = $this->get(SerializerInterface::class);
    }

    abstract protected function getEndpoint(): string;

    /**
     * @return array<string, array<int, array<string, array<string, bool|string>|string>>>
     */
    public static function provideInvalidQueryString(): array
    {
        return [
            'name is longer than 255 characters' => [
                [
                    'name' => ByteString::fromRandom(256)->toString(),
                ],
            ],
            'op is not valid' => [
                [
                    'type' => 'invalidOp',
                ],
            ],
            'quantity amount is not numeric or string' => [
                [
                    'quantity' => [
                        'amount' => false,
                    ],
                ],
            ],
            'quantity op is not valid' => [
                [
                    'quantity' => [
                        'op' => 'invalidOp',
                    ],
                ],
            ],
            'quantity unit is not valid' => [
                [
                    'quantity' => [
                        'unit' => 'invalidUnit',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, array<int, array<string, array<string, bool|string>|string>>> $invalidQueryString
     */
    #[DataProvider('provideInvalidQueryString')]
    public function testItDoesNotAllowInvalidQueryString(array $invalidQueryString): void
    {
        $this->client->jsonRequest(
            method: 'GET',
            uri: $this->getEndpoint() . '?' . http_build_query($invalidQueryString),
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * https://phpstan.org/blog/whats-up-with-template-covariant
     *
     * @param CollectionWrapper<Edible> $existingEdibles
     * @param array<string, mixed> $queryString
     * @param ?CollectionWrapper<Edible> $expectedEdibles
     */
    protected function testItListsEdibles(
        CollectionWrapper $existingEdibles,
        array $queryString = [],
        ?CollectionWrapper $expectedEdibles = null
    ): void {
        $this->client->jsonRequest(
            method: 'GET',
            uri: count($queryString) === 0
                ? $this->getEndpoint()
                : $this->getEndpoint() . '?' . http_build_query($queryString)
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals(
            $this->serializer->serialize($expectedEdibles ?? $existingEdibles, 'json'),
            $response->getContent(),
        );
    }
}