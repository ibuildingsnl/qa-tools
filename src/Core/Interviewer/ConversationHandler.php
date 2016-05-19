<?php
namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;

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

    /**
     * @param Sentence $sentence
     * @return void
     */
    public function say(Sentence $sentence);

    /**
     * @param Sentence $sentence
     * @return void
     */
    public function error(Sentence $sentence);

    /**
     * @param Sentence $sentence
     * @return void
     */
    public function comment(Sentence $sentence);
}
