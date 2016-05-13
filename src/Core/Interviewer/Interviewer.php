<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Value\Answer\Answer;
use Ibuildings\QaTools\Value\Question\Question;
use Ibuildings\QaTools\Value\Sentence;

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

    /**
     * @param Sentence $sentence
     */
    public function comment(Sentence $sentence)
    {
        $this->conversationHandler->comment($sentence);
    }
}
