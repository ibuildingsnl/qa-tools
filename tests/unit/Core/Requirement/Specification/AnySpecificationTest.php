<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\Specification\AnySpecification;
use Ibuildings\QaTools\Core\Requirement\Specification\Specification;
use Mockery;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Requirement
 */
class AnySpecificationTest extends TestCase
{
    /** @test */
    public function any_requirement_satisfies_it()
    {
        $specification = new AnySpecification();

        $this->assertTrue($specification->isSatisfiedBy(Mockery::mock(Requirement::class)));
    }

    /** @test */
    public function are_all_equal()
    {
        $specA = new AnySpecification();
        $specB = new AnySpecification();
        $otherSpec = Mockery::mock(Specification::class);

        $this->assertTrue($specA->equals($specB));
        $this->assertFalse($specA->equals($otherSpec));
    }
}
