<?php

declare(strict_types=1);

namespace App\Tests;

use App\Shared\Infrastructure\Http\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

abstract class KernelTestCase extends BaseKernelTestCase
{
    /**
     * @template T of object
     * @param class-string<T> $className
     * @return T
     */
    protected function get(string $className)
    {
        /** @var ?T $instance */
        $instance = self::getContainer()->get($className);

        if (is_null($instance)) {
            throw new ServiceNotFoundException($className);
        }

        return $instance;
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}