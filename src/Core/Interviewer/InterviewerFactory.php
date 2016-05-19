<?php

namespace Ibuildings\QaTools\Core\Interviewer;

class InterviewerFactory
{
    /**
     * @param ConversationHandler $conversationHandler
     * @return Interviewer
     */
    public function createWith(
        ConversationHandler $conversationHandler
    ) {
        return new Interviewer($conversationHandler);
    }
}
