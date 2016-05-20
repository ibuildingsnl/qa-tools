<?php
namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;

interface InterviewerInterface
{
    /**
     * @param Question $question
     * @return Answer
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
