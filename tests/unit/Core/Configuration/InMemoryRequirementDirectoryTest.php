<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\InMemoryRequirementDirectory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Requirement\RequirementList;
use Ibuildings\QaTools\Core\Requirement\Specification\AnySpecification;
use Ibuildings\QaTools\Core\Requirement\Specification\EqualsSpecification;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Ibuildings\QaTools\UnitTest\Core\Requirement\FooRequirement;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Configuration
 * @group Requirement
 */
class InMemoryRequirementDirectoryTest extends TestCase
{
    /**
     * @test
     */
    public function requirement_directory_initializes_with_an_empty_requirementlist()
    {
        $dummyProject = Mockery::mock(Project::class);

        $requirementDirectory = new InMemoryRequirementDirectory($dummyProject);

        $this->assertCount(
            0,
            $requirementDirectory->getRequirements(),
            'Requirement directory ought to contain no registered requirements'
        );
    }

    /**
     * @test
     */
    public function the_project_given_during_instantiation_can_be_retrieved_from_the_requirement_directory()
    {
        $dummyProject = Mockery::mock(Project::class);

        $requirementDirectory = new InMemoryRequirementDirectory($dummyProject);

        $retrievedProject = $requirementDirectory->getProject();

        $this->assertEquals($dummyProject, $retrievedProject);
    }

    /**
     * @test
     */
    public function a_requirement_can_be_registered()
    {
        $dummyProject = Mockery::mock(Project::class);

        $fakeRequirement = new FooRequirement('Some requirement');

        $requirementDirectory = new InMemoryRequirementDirectory($dummyProject);
        $requirementDirectory->registerRequirement($fakeRequirement);

        $this->assertTrue(
            $requirementDirectory->getRequirements()->equals(new RequirementList([$fakeRequirement])),
            'Requirement directory ought to contain the registered requirement'
        );
    }
}
