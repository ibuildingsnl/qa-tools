<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

/**
 * Executes its supported tasks.
 *
 * Users of Executors must guarantee they call their methods in the following order:
 *  - arePrerequisitesMet()
 *  - execute()
 *  - cleanUp()
 *
 * After the Executor has been execute()d, its rollBack() method may be called. Its
 * rollBack() method is never called directly after arePrerequisitesMet(). It may be
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
     * @param Project     $project
     * @param Interviewer $interviewer
     * @return bool
     */
    public function arePrerequisitesMet(TaskList $tasks, Project $project, Interviewer $interviewer);

    /**
     * @param TaskList    $tasks
     * @param Project     $project
     * @param Interviewer $interviewer
     * @return void
     */
    public function execute(TaskList $tasks, Project $project, Interviewer $interviewer);

    /**
     * @param TaskList    $tasks
     * @param Project     $project
     * @param Interviewer $interviewer
     * @return void
     */
    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer);

    /**
     * @param TaskList    $tasks
     * @param Project     $project
     * @param Interviewer $interviewer
     * @return void
     */
    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer);
}
