<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\Specification\AnySpecification;
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
}
