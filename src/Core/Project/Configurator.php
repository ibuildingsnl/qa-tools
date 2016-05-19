<?php

namespace Ibuildings\QaTools\Core\Project;

use Ibuildings\QaTools\Core\Interviewer\ConversationHandler;
use Ibuildings\QaTools\Core\Interviewer\InterviewerFactory;

final class Configurator
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
     * @param ConversationHandler $conversationHandler
     */
    public function configure(ConversationHandler $conversationHandler)
    {
        // Implementation
    }
}
