<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\InMemoryTaskDirectory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\AnySpecification;
use Ibuildings\QaTools\Core\Task\EqualsSpecification;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Ibuildings\QaTools\UnitTest\Core\Task\FakeTask;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Configuration
 * @group Task
 */
class InMemoryTaskDirectoryTest extends TestCase
{
    /**
     * @test
     */
    public function task_directory_initializes_with_an_empty_tasklist()
    {
        $dummyProject = Mockery::mock(Project::class);

        $taskDirectory = new InMemoryTaskDirectory($dummyProject);

        $this->assertCount(
            0,
            $taskDirectory->matchTasks(new AnySpecification()),
            'Task directory ought to contain no registered tasks'
        );
    }

    /**
     * @test
     */
    public function the_project_given_during_instantiation_can_be_retrieved_from_the_task_directory()
    {
        $dummyProject = Mockery::mock(Project::class);

        $taskDirectory = new InMemoryTaskDirectory($dummyProject);

        $retrievedProject = $taskDirectory->getProject();

        $this->assertEquals($dummyProject, $retrievedProject);
    }

    /**
     * @test
     */
    public function a_task_can_be_registered()
    {
        $dummyProject = Mockery::mock(Project::class);

        $fakeTask = new FakeTask('Some task');

        $taskDirectory = new InMemoryTaskDirectory($dummyProject);
        $taskDirectory->registerTask($fakeTask, PhpMd::class);

        $this->assertTrue(
            $taskDirectory->matchTasks(new EqualsSpecification($fakeTask))->equals(new TaskList([$fakeTask])),
            'Task directory ought to contain the registered task'
        );
    }
}
