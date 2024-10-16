<?php

declare(strict_types=1);

namespace App\Edible\Infrastructure\Http;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Unit;
use App\Shared\Domain\Collection\CollectionWrapper;

final class UnitsConverter
{
    /**
     * @param Edible|CollectionWrapper<Edible> $data
     * @param Unit|null $convertTo
     */
    public function convert(Edible|CollectionWrapper $data, ?Unit $convertTo): void
    {
        if (is_null($convertTo)) {
            return;
        }

        $data instanceof Edible
            ? $this->convertEdibleUnit($data, $convertTo)
            : $data->each(fn(Edible $edible) => $this->convertEdibleUnit($edible, $convertTo));
    }

    private function convertEdibleUnit(Edible $edible, Unit $convertTo): void
    {
        $edible->setQuantity($edible->getQuantity()->convertTo($convertTo));
    }
}