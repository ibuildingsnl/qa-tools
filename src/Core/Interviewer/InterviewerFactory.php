<?php

namespace Ibuildings\QaTools\Core\Interviewer;

class InterviewerFactory
{
    /**
     * @param ConversationHandler $questionHandler
     * @return Interviewer
     */
    public function createWith(ConversationHandler $questionHandler)
    {
        return new Interviewer($questionHandler);
    }
}
