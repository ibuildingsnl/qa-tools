<?php

namespace Ibuildings\QaTools\Core\Task\Compiler;

use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

/**
 * Compiles a queue of tasks to execute from the requirement directory set up
 * during the configuration stage.
 */
interface TaskListCompiler
{
    /**
     * @param RequirementDirectory $requirementDirectory
     * @param ScopedInterviewer    $interviewer
     * @return \Ibuildings\QaTools\Core\Task\TaskList
     */
    public function compile(RequirementDirectory $requirementDirectory, ScopedInterviewer $interviewer);
}
