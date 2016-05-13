<?php
namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Value\Answer\Answer;
use Ibuildings\QaTools\Value\Question\Question;

interface ConversationHandler
{
    /**
     * @param Question $question
     * @return Answer $answer
     */
    public function ask(Question $question);

    /**
     * @param Question $question
     * @return Answer $answer
     */
    public function askHidden(Question $question);
}
