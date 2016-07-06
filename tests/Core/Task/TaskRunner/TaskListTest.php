<?php

use Ibuildings\QaTools\Core\Task\FakeTask;
use Ibuildings\QaTools\Core\Task\Runner\TaskList;
use Ibuildings\QaTools\Core\Task\Task;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Task
 */
class TaskListTest extends TestCase
{
    /**
     * @test
     */
    public function tasklist_can_be_filtered_according_to_a_predicate_resulting_in_a_tasklist_of_the_filtered_tasks()
    {
        $taskMockThatShouldRemain = Mockery::mock(Task::class);
        $taskMockThatShouldRemain->shouldReceive('remains')
            ->andReturn(true);

        $taskMockThatShouldNotRemain = Mockery::mock(Task::class);
        $taskMockThatShouldNotRemain->shouldReceive('remains')
            ->andReturn(false);

        $taskList = new TaskList([$taskMockThatShouldRemain, $taskMockThatShouldNotRemain]);
        $expectedFilterResult = new TaskList([$taskMockThatShouldRemain]);

        $actualFilterResult = $taskList->filter(function (Task $task) {
            return $task->remains();
        });

        $this->assertEquals($expectedFilterResult, $actualFilterResult);
    }

    /**
     * @test
     */
    public function two_tasklists_with_overlapping_tasks_are_merged_resulting_in_one_tasklist()
    {
        $fakeTaskA = new FakeTask('A');
        $fakeTaskB = new FakeTask('B');

        $taskListA = new TaskList([$fakeTaskA]);
        $taskListB = new TaskList([$fakeTaskA, $fakeTaskB]);

        $expectedMergeResult = new TaskList([$fakeTaskA, $fakeTaskB]);

        $actualMergeResult = $taskListA->merge($taskListB);

        $this->assertEquals($expectedMergeResult, $actualMergeResult);
    }

    /**
     * @test
     */
    public function two_tasklists_without_overlapping_tasks_are_merged_resulting_in_one_tasklist()
    {
        $fakeTaskA = new FakeTask('A');
        $fakeTaskB = new FakeTask('B');

        $taskListA = new TaskList([$fakeTaskA]);
        $taskListB = new TaskList([$fakeTaskB]);

        $expectedMergeResult = new TaskList([$fakeTaskA, $fakeTaskB]);

        $actualMergeResult = $taskListA->merge($taskListB);

        $this->assertEquals($expectedMergeResult, $actualMergeResult);
    }

    /**
     * @test
     */
    public function a_task_is_added_to_a_tasklist()
    {
        $task = new FakeTask('A');
        $taskList = new TaskList([]);

        $appendedTaskList = $taskList->add($task);

        $this->assertFalse($taskList->contains($task));
        $this->assertTrue($appendedTaskList->contains($task));
    }

    /**
     * @test
     */
    public function tasklist_with_two_tasks_counts_as_two()
    {
        $fakeTaskA = new FakeTask('A');
        $fakeTaskB = new FakeTask('B');

        $taskList = new TaskList([$fakeTaskA, $fakeTaskB]);

        $this->assertEquals(2, count($taskList));
    }

    /**
     * @test
     */
    public function tasklist_with_no_tasks_counts_as_zero()
    {
        $taskList = new TaskList([]);

        $this->assertEquals(0, count($taskList));
    }

    /**
     * @test
     */
    public function tasklist_is_iterable()
    {
        $fakeTaskA = new FakeTask('A');
        $fakeTaskB = new FakeTask('B');

        $taskList = new TaskList([$fakeTaskA, $fakeTaskB]);

        foreach ($taskList as $task) {
            $this->assertTrue($task->equals($fakeTaskA) || $task->equals($fakeTaskB));
        }
    }
}
