<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;

interface Question
{
    /**
     * @param Answer $answer
     * @return static
     */
    public function withDefaultAnswer(Answer $answer);

    /**
     * @return bool
     */
    public function hasDefaultAnswer();

    /**
     * @return Answer
     */
    public function getDefaultAnswer();

    /**
     * @return string $question
     */
    public function getQuestion();

    public function __toString();
}
