<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Requirement\Specification\TypeSpecification;
use Ibuildings\QaTools\UnitTest\Core\Requirement\BarRequirement;
use Ibuildings\QaTools\UnitTest\Core\Requirement\FooRequirement;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Requirement
 */
class TypeSpecificationTest extends TestCase
{
    /** @test */
    public function a_requirement_of_the_specified_type_ought_to_satisfy_the_specification()
    {
        $specification = new TypeSpecification(FooRequirement::class);
        $this->assertTrue(
            $specification->isSatisfiedBy(new FooRequirement('quux')),
            'FooRequirement did not satisfy the specification, even though it should'
        );
    }

    /** @test */
    public function a_requirement_of_a_different_type_ought_not_to_satisfy_the_specification()
    {
        $specification = new TypeSpecification(BarRequirement::class);
        $this->assertTrue(
            $specification->isSatisfiedBy(new BarRequirement()),
            "BarRequirement satisfied the specification, while it should'nt"
        );
    }
}
