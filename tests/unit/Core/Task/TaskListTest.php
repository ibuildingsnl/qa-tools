<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task;

use Ibuildings\QaTools\Core\Task\Specification\Specification;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\UnitTest\Diffing;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Task
 */
class TaskListTest extends TestCase
{
    use Diffing;

    /** @test */
    public function tasklist_can_be_counted()
    {
        $fakeTaskA = Mockery::mock(Task::class);
        $fakeTaskB = Mockery::mock(Task::class);

        $taskList = new TaskList([$fakeTaskA, $fakeTaskB]);

        $this->assertCount(2, $taskList);
    }

    /** @test */
    public function tasks_can_be_added_to_a_tasklist()
    {
        $taskA = new FakeTask('A');
        $taskB = new FakeTask('B');
        $taskList = new TaskList();

        $addedTaskList = $taskList->add($taskA)->add($taskB);
        $expectedTaskList = new TaskList([$taskA, $taskB]);

        $this->assertTrue(
            $expectedTaskList->equals($addedTaskList),
            $this->diff($expectedTaskList, $addedTaskList, 'Task list different than expected')
        );
    }

    /** @test */
    public function tasks_can_be_prepended_to_a_tasklist()
    {
        $taskA = new FakeTask('A');
        $taskB = new FakeTask('B');
        $taskList = new TaskList();

        $prependedTaskList = $taskList->prepend($taskA)->prepend($taskB);
        $expectedTaskList = new TaskList([$taskB, $taskA]);

        $this->assertTrue(
            $expectedTaskList->equals($prependedTaskList),
            $this->diff($expectedTaskList, $prependedTaskList, 'Task list different than expected')
        );
    }

    /** @test */
    public function tasklist_with_no_tasks_counts_as_zero()
    {
        $taskList = new TaskList();

        $this->assertCount(0, $taskList);
    }

    /** @test */
    public function tasklist_is_iterable()
    {
        $fakeTaskA = Mockery::mock(Task::class);
        $fakeTaskB = Mockery::mock(Task::class);

        $taskList = new TaskList([$fakeTaskA, $fakeTaskB]);

        $tasks = [$fakeTaskA, $fakeTaskB];
        foreach ($taskList as $task) {
            $this->assertSame($task, array_shift($tasks));
        }
    }

    /** @test */
    public function can_match_tasks_in_its_list()
    {
        $taskA = new FakeTask('A');
        $taskB = new FakeTask('B');
        $taskList = new TaskList([$taskA, $taskB]);

        $specification = Mockery::mock(Specification::class);
        $specification->shouldReceive('isSatisfiedBy')->with($taskA)->andReturn(true);
        $specification->shouldReceive('isSatisfiedBy')->with($taskB)->andReturn(false);

        $expectedMatchingTasks = new TaskList([$taskA]);
        $actualMatchingTasks = $taskList->match($specification);

        $this->assertTrue(
            $expectedMatchingTasks->equals($actualMatchingTasks),
            $this->diff($expectedMatchingTasks, $actualMatchingTasks, "Unexpected tasks matched/didn't match")
        );
    }
}
