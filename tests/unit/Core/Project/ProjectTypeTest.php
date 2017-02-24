<?php

namespace Ibuildings\QaTools\UnitTest\Core\Project;

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Test\MockeryTestCase;

/**
 * @group Project
 */
class ProjectTypeTest extends MockeryTestCase
{
    /** @test */
    public function equals_instance_of_the_same_project_type()
    {
        $this->assertTrue(
            (new ProjectType(ProjectType::TYPE_PHP_DRUPAL_8))->equals(new ProjectType(ProjectType::TYPE_PHP_DRUPAL_8)),
            'Two equal project types should be equal'
        );
    }

    /** @test */
    public function must_be_one_of_the_recognised_types()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is not an element of the valid values');

        new ProjectType('ruby.ror');
    }

    /** @test */
    public function can_be_created_from_a_human_readable_string()
    {
        $this->assertTrue(
            ProjectType::fromHumanReadableString('Symfony 3')->equals(new ProjectType(ProjectType::TYPE_PHP_SF_3)),
            'Project type created from human readable string ought to equal project type created from constant'
        );
    }
}
