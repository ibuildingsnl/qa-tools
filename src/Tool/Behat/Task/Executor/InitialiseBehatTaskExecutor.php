<?php

namespace Ibuildings\QaTools\Tool\Behat\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Executor\Executor;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Tool\Behat\Initialiser\BehatInitialiser;
use Ibuildings\QaTools\Tool\Behat\Task\InitialiseBehatTask;

final class InitialiseBehatTaskExecutor implements Executor
{
    /**
     * @var BehatInitialiser
     */
    private $initialiser;

    public function __construct(BehatInitialiser $initialiser)
    {
        $this->initialiser = $initialiser;
    }

    /**
     * @param Task $task
     *
     * @return bool
     */
    public function supports(Task $task)
    {
        return $task instanceof InitialiseBehatTask;
    }

    /**
     * @param TaskList    $tasks
     * @param Project     $project
     * @param Interviewer $interviewer
     *
     * @return bool
     */
    public function arePrerequisitesMet(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        return true;
    }

    /**
     * @param TaskList    $tasks
     * @param Project     $project
     * @param Interviewer $interviewer
     *
     * @return void
     */
    public function execute(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->notice(" * Initialising Behat...");

        $this->initialiser->initialise($project->getRootDirectory());
    }

    /**
     * @param TaskList    $tasks
     * @param Project     $project
     * @param Interviewer $interviewer
     *
     * @return void
     */
    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
    }

    /**
     * @param TaskList    $tasks
     * @param Project     $project
     * @param Interviewer $interviewer
     *
     * @return void
     */
    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
    }
}
