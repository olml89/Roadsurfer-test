<?php

declare(strict_types=1);

namespace App\Tests\Unit\Edible\Domain\Specification;

use App\Edible\Domain\Edible;
use App\Edible\Domain\Quantity;
use App\Edible\Domain\Specification\EdibleAndSpecification;
use App\Edible\Domain\Specification\EdibleNotSpecification;
use App\Edible\Domain\Specification\EdibleOrSpecification;
use App\Edible\Domain\Specification\NameContains;
use App\Edible\Domain\Specification\QuantityComparesTo;
use App\Edible\Domain\Unit;
use App\Shared\Domain\Criteria\Filter\Operator;
use App\Tests\Unit\Edible\Domain\Fixtures\NotValidatedEdibleFactory;
use PHPUnit\Framework\TestCase;

final class EdibleCompositeSpecificationsTest extends TestCase
{
    protected Edible $edible;
    protected NameContains $nameContains;
    protected QuantityComparesTo $quantityComparesTo;

    protected function setUp(): void
    {
        $this->edible = NotValidatedEdibleFactory::generate(
            name: 'John Smith',
            quantity: Quantity::create(amount: 100, unit: Unit::g),
        );

        $this->nameContains = new NameContains('John Smith');

        $this->quantityComparesTo = new QuantityComparesTo(
            Operator::GT,
            Quantity::create(amount: 150,unit: Unit::g),
        );
    }

    public function testEdibleNotSpecification(): void
    {
        $notNameContains = new EdibleNotSpecification($this->nameContains);
        $notQuantityComparesTo = new EdibleNotSpecification($this->quantityComparesTo);

        $this->assertTrue($this->nameContains->isSatisfiedBy($this->edible));
        $this->assertFalse($notNameContains->isSatisfiedBy($this->edible));

        $this->assertFalse($this->quantityComparesTo->isSatisfiedBy($this->edible));
        $this->assertTrue($notQuantityComparesTo->isSatisfiedBy($this->edible));
    }

    public function testEdibleAndSpecification(): void
    {
        $andSpecification = new EdibleAndSpecification($this->nameContains, $this->quantityComparesTo);

        $this->assertTrue($this->nameContains->isSatisfiedBy($this->edible));
        $this->assertFalse($this->quantityComparesTo->isSatisfiedBy($this->edible));
        $this->assertFalse($andSpecification->isSatisfiedBy($this->edible));
    }

    public function testEdibleOrSpecification(): void
    {
        $orSpecification = new EdibleOrSpecification($this->nameContains, $this->quantityComparesTo);

        $this->assertTrue($this->nameContains->isSatisfiedBy($this->edible));
        $this->assertFalse($this->quantityComparesTo->isSatisfiedBy($this->edible));
        $this->assertTrue($orSpecification->isSatisfiedBy($this->edible));
    }
}