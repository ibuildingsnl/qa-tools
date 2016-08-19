<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;

interface Task
{
    /**
     * Returns a single-line description of what this task will attempt to effect.
     *
     * @return string
     */
    public function getDescription();

    /**
     * @param Interviewer $interviewer
     * @return void
     */
    public function checkPrerequisites(Interviewer $interviewer);

    /**
     * @param Interviewer $interviewer
     * @return Task A task that, when executed, rolls back the changes this task effected.
     */
    public function execute(Interviewer $interviewer);

    /**
     * @param Task $task
     * @return bool
     */
    public function equals(Task $task);
}
