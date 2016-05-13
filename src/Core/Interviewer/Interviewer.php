<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Value\Answer\Answer;
use Ibuildings\QaTools\Value\Question\Question;

final class Interviewer
{
    /**
     * @var ConversationHandler
     */
    private $conversationHandler;

    public function __construct(ConversationHandler $questionHandler)
    {
        $this->conversationHandler = $questionHandler;
    }

    /**
     * @param Question $question
     * @return Answer
     */
    public function ask(Question $question)
    {
        return $this->conversationHandler->ask($question);
    }

    /**
     * @param Question $question
     * @return Answer
     */
    public function askHidden(Question $question)
    {
        return $this->conversationHandler->askHidden($question);
    }
}
