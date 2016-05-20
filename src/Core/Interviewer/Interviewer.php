<?php
namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;

interface Interviewer
{
    /**
     * @param Question $question
     * @return Answer
     */
    public function ask(Question $question);

    /**
     * @param $sentence
     */
    public function say($sentence);

    /**
     * @param $sentence
     */
    public function warn($sentence);
}
