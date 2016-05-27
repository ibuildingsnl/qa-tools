<?php
namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question as QuestionInterface;

interface Interviewer
{
    /**
     * @param QuestionInterface $question
     * @return Answer
     */
    public function ask(QuestionInterface $question);

    /**
     * @param $sentence
     */
    public function say($sentence);

    /**
     * @param $sentence
     */
    public function warn($sentence);
}
