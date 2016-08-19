<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;

/**
 * A task is an operation that can be executed once, and whose changes can be
 * rolled back.
 */
interface Task
{
    /**
     * Returns a single-line description of what this task will attempt to effect.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Checks the prerequisites for executing this task, like e.g. verifying file
     * system writeability.
     *
     * @param Interviewer $interviewer
     * @return void
     */
    public function checkPrerequisites(Interviewer $interviewer);

    /**
     * Executes this task. The task may only be executed once.
     *
     * @param Interviewer $interviewer
     * @return void
     */
    public function execute(Interviewer $interviewer);

    /**
     * Rolls back the changes this task's execution effected. This rollback may only be
     * executed once, and only after the task has been executed.
     *
     * @param Interviewer $interviewer
     * @return void
     */
    public function rollBack(Interviewer $interviewer);

    /**
     * @param Task $task
     * @return bool
     */
    public function equals(Task $task);
}
