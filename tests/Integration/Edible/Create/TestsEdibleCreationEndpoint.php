<?php

declare(strict_types=1);

namespace App\Tests\Integration\Edible\Create;

use App\Edible\Domain\Type;
use App\Edible\Domain\Unit;
use App\Tests\Helpers\ProvidesEdibleCreationData;
use App\Tests\KernelTestCase;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\ByteString;

/**
 * @mixin KernelTestCase
 */
trait TestsEdibleCreationEndpoint
{
    use ProvidesEdibleCreationData;

    protected KernelBrowser $client;
    protected SerializerInterface $serializer;
    protected NotValidatedEdibleFactory $edibleFactory;

    abstract protected function getEndpoint(): string;

    protected function setUp(): void
    {
        $this->client = new KernelBrowser(self::bootKernel());
        $this->serializer = $this->get(SerializerInterface::class);
        $this->edibleFactory = new NotValidatedEdibleFactory();
    }

    /**
     * @return array<string, array{array<'id'|'name'|'quantity'|'type'|'unit'|'extra_field', mixed>}>
     */
    public static function provideInvalidRequestPayload(): array
    {
        return [
            'id is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['id']),
                ),
            ],
            'id is blank' => [
                self::edibleData(id: ''),
            ],
            'id is not an integer' => [
                self::edibleData(id: 3.1416),
            ],
            'id is not positive' => [
                self::edibleData(id: -1),
            ],
            'name is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['name']),
                ),
            ],
            'name is blank' => [
                self::edibleData(name: ''),
            ],
            'name is longer than 255 characters' => [
                self::edibleData(name: ByteString::fromRandom(256)->toString()),
            ],
            'type is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['type']),
                ),
            ],
            'type is blank' => [
                self::edibleData(type: ''),
            ],
            'type is not valid' => [
                self::edibleData(type: 'type'),
            ],
            'quantity is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['quantity']),
                ),
            ],
            'quantity is blank' => [
                self::edibleData(quantity: false),
            ],
            'quantity is not numeric' => [
                self::edibleData(quantity: 'six'),
            ],
            'quantity is not positive' => [
                self::edibleData(quantity: -12.5),
            ],
            'unit is missing' => [
                array_diff_key(
                    self::edibleData(),
                    array_flip(['unit']),
                ),
            ],
            'unit is blank' => [
                self::edibleData(unit: false),
            ],
            'unit is not valid' => [
                self::edibleData(unit: 'unit'),
            ],
            'extra field' => [
                array_merge(
                    self::edibleData(),
                    [
                        'extra_field' => 'random',
                    ],
                ),
            ],
        ];
    }

    /**
     * @param array<string, array{array<'id'|'name'|'quantity'|'type'|'unit'|'extra_field', mixed>}> $invalidPayload
     */
    #[DataProvider('provideInvalidRequestPayload')]
    public function testItDoesNotAllowInvalidRequestPayload(array $invalidPayload): void
    {
        $this->client->jsonRequest(
            method: 'POST',
            uri: $this->getEndpoint(),
            parameters: $invalidPayload,
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    protected function testItCreatesEdible(Type $edibleType): void
    {
        /** @var array{id: int, name: string, type: value-of<Type>, quantity: float, unit: value-of<Unit>} $edibleData */
        $edibleData = self::edibleData(type: $edibleType->value);
        $expectedEdible = $this->edibleFactory->create($edibleData);

        $this->client->jsonRequest(
            method: 'POST',
            uri: $this->getEndpoint(),
            parameters: $edibleData,
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertEquals(
            $this->serializer->serialize($expectedEdible, 'json'),
            $response->getContent(),
        );
    }
}