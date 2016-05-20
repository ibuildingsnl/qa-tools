<?php
namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;

interface ConversationHandler
{
    /**
     * @param Question $question
     * @return Answer $answer
     */
    public function ask(Question $question);

    /**
     * @param Sentence $sentence
     */
    public function say(Sentence $sentence);

    /**
     * @param Sentence $sentence
     */
    public function error(Sentence $sentence);
}
