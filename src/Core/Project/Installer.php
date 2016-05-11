<?php

namespace Ibuildings\QaTools\Core\Project;

use Ibuildings\QaTools\Core\Interviewer\ConversationHandler;
use Ibuildings\QaTools\Core\Interviewer\InterviewerFactory;

final class Installer
{
    /**
     * @var InterviewerFactory
     */
    private $interviewerFactory;

    public function __construct(InterviewerFactory $interviewerFactory)
    {
        $this->interviewerFactory = $interviewerFactory;
    }

    public function install(ConversationHandler $questionHandler)
    {
        // Implementation
    }
}
