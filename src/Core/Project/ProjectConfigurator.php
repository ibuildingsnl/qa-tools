<?php

namespace Ibuildings\QaTools\Core\Project;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\Cli\InterviewerFactory;

final class ProjectConfigurator
{
    /**
     * @var InterviewerFactory
     */
    private $interviewerFactory;

    public function __construct(InterviewerFactory $interviewerFactory)
    {
        $this->interviewerFactory = $interviewerFactory;
    }

    /**
     * @param Interviewer $interviewer
     */
    public function configure(Interviewer $interviewer)
    {
        // Implementation
    }
}
