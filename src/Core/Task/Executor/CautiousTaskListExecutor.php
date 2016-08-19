<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Exception;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

/**
 * This task list executor cautiously executes all tasks. First, all prerequisites
 * are checked. If a prerequisites is not met, execution is aborted. Secondly, all
 * tasks are executed in-order. Should a task fail, despite having checked its
 * prerequisites, the rollback tasks returned by the previously executed tasks are
 * executed in reverse order.
 */
final class CautiousTaskListExecutor implements TaskListExecutor
{
    public function execute(TaskList $tasks, ScopedInterviewer $interviewer)
    {
        $this->checkAllPrerequisites($tasks, $interviewer);
        $this->executeAll($tasks, $interviewer);
    }

    /**
     * @param TaskList          $tasks
     * @param ScopedInterviewer $interviewer
     */
    private function checkAllPrerequisites(TaskList $tasks, ScopedInterviewer $interviewer)
    {
        foreach ($tasks as $task) {
            $interviewer->setScope(get_class($task));
            $task->checkPrerequisites($interviewer);
        }
    }

    /**
     * @param TaskList    $tasks
     * @param Interviewer $interviewer
     */
    private function executeAll(TaskList $tasks, Interviewer $interviewer)
    {
        $tasksToRollback = new TaskList();

        try {
            foreach ($tasks as $task) {
                /** @var Task $task */
                $tasksToRollback = $tasksToRollback->prepend($task);
                $task->execute($interviewer);

                $interviewer->say(sprintf('  %-6s  %s', '[OK]', $task->getDescription()));
            }
        } catch (Exception $e) {
            $interviewer->warn(sprintf('  %-6s  %s', '[FAIL]', $task->getDescription()));
            $interviewer->say('Attempting a roll-back...');

            foreach ($tasksToRollback as $task) {
                try {
                    $task->rollBack($interviewer);
                    $interviewer->say(sprintf('  %-6s  %s', '[OK]', $task->getDescription()));
                } catch (Exception $e) {
                    $interviewer->warn(sprintf('  %-6s  %s', '[FAIL]', $task->getDescription()));
                    $interviewer->say(
                        sprintf(
                            'The roll-back task "%s" for task "%s" failed: "%s"',
                            $task->getDescription(),
                            $task->getDescription(),
                            $e->getMessage()
                        )
                    );
                }
            }
        }
    }
}
