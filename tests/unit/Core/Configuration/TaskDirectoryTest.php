<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Runner\TaskList;
use Ibuildings\QaTools\UnitTest\Core\Task\FakeTask;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Configuration
 * @group Task
 */
class TaskDirectoryTest extends TestCase
{
    /**
     * @test
     */
    public function task_directory_initializes_with_an_empty_tasklist()
    {
        $dummyProject = Mockery::mock(Project::class);

        $taskDirectory = new TaskDirectory($dummyProject);

        $expectedTaskList = new TaskList([]);
        $actualTaskList   = $taskDirectory->getTaskList();

        $this->assertEquals($expectedTaskList, $actualTaskList);
    }

    /**
     * @test
     */
    public function the_project_given_during_instantiation_can_be_retrieved_from_the_task_directory()
    {
        $dummyProject = Mockery::mock(Project::class);

        $taskDirectory = new TaskDirectory($dummyProject);

        $retrievedProject = $taskDirectory->getProject();

        $this->assertEquals($dummyProject, $retrievedProject);
    }

    /**
     * @test
     */
    public function a_task_can_be_added_to_the_task_directorys_task_list()
    {
        $dummyProject = Mockery::mock(Project::class);

        $fakeTask         = new FakeTask('Some task');
        $expectedTaskList = new TaskList([$fakeTask]);

        $taskDirectory = new TaskDirectory($dummyProject);
        $taskDirectory->addTask($fakeTask);

        $actualTaskList = $taskDirectory->getTaskList();

        $this->assertEquals($expectedTaskList, $actualTaskList);
    }
}
