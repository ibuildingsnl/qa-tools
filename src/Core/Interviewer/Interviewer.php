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
     * @param string $sentence
     */
    public function say($sentence);

    /**
     * @param string $sentence
     */
    public function warn($sentence);
}
