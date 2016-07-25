<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\TaskRegistry;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Runner\TaskList;
use Ibuildings\QaTools\UnitTest\Core\Task\FakeTask;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Configuration
 * @group Task
 */
class TaskRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function task_registry_initializes_with_an_empty_tasklist()
    {
        $dummyProject = Mockery::mock(Project::class);

        $taskRegistry = new TaskRegistry($dummyProject);

        $expectedTaskList = new TaskList([]);
        $actualTaskList   = $taskRegistry->getTaskList();

        $this->assertEquals($expectedTaskList, $actualTaskList);
    }

    /**
     * @test
     */
    public function the_project_given_during_instantiation_can_be_retrieved_from_the_task_registry()
    {
        $dummyProject = Mockery::mock(Project::class);

        $taskRegistry = new TaskRegistry($dummyProject);

        $retrievedProject = $taskRegistry->getProject();

        $this->assertEquals($dummyProject, $retrievedProject);
    }

    /**
     * @test
     */
    public function a_task_can_be_added_to_the_task_registrys_task_list()
    {
        $dummyProject = Mockery::mock(Project::class);

        $fakeTask         = new FakeTask('Some task');
        $expectedTaskList = new TaskList([$fakeTask]);

        $taskRegistry = new TaskRegistry($dummyProject);
        $taskRegistry->addTask($fakeTask);

        $actualTaskList = $taskRegistry->getTaskList();

        $this->assertEquals($expectedTaskList, $actualTaskList);
    }
}
