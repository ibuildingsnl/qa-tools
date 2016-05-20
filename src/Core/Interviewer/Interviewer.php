<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;

final class Interviewer implements InterviewerInterface
{
    /**
     * @var ConversationHandler
     */
    private $conversationHandler;

    public function __construct(ConversationHandler $conversationHandler)
    {
        $this->conversationHandler = $conversationHandler;
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
     * @param Sentence $sentence
     */
    public function say(Sentence $sentence)
    {
        $this->conversationHandler->say($sentence);
    }

    /**
     * @param Sentence $sentence
     */
    public function error(Sentence $sentence)
    {
        $this->conversationHandler->error($sentence);
    }
}
