<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

/**
 * Executes its supported tasks.
 *
 * Users of Executors must guarantee they call their methods in the following order:
 *  - checkPrerequisites()
 *  - execute()
 *  - cleanUp()
 *
 * After the Executor has been execute()d, its rollBack() method may be called. Its
 * rollBack() method is never called directly after checkPrerequisites(). It may be
 * called after cleanUp().
 */
interface Executor
{
    /**
     * @param Task $task
     * @return bool
     */
    public function supports(Task $task);

    /**
     * @param TaskList    $tasks
     * @param Interviewer $interviewer
     * @return void
     */
    public function checkPrerequisites(TaskList $tasks, Interviewer $interviewer);

    /**
     * @param TaskList    $tasks
     * @param Interviewer $interviewer
     * @return void
     */
    public function execute(TaskList $tasks, Interviewer $interviewer);

    /**
     * @param TaskList    $tasks
     * @param Interviewer $interviewer
     * @return void
     */
    public function cleanUp(TaskList $tasks, Interviewer $interviewer);

    /**
     * @param TaskList    $tasks
     * @param Interviewer $interviewer
     * @return void
     */
    public function rollBack(TaskList $tasks, Interviewer $interviewer);
}
