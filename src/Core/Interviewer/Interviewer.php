<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Value\Question\Question;

final class Interviewer
{
    /**
     * @var ConversationHandler
     */
    private $questionHandler;

    public function __construct(ConversationHandler $questionHandler)
    {
        $this->questionHandler = $questionHandler;
    }

    public function ask(Question $question)
    {
        return $this->questionHandler->ask($question);
    }
}
