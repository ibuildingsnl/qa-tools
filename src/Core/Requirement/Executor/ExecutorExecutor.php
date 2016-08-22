<?php

namespace Ibuildings\QaTools\Core\Requirement\Executor;

use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

interface ExecutorExecutor
{
    /**
     * @param RequirementDirectory $requirementDirectory
     * @param ScopedInterviewer    $interviewer
     * @return void
     */
    public function execute(RequirementDirectory $requirementDirectory, ScopedInterviewer $interviewer);
}
