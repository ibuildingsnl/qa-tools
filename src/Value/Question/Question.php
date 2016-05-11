<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Value\Answer\Answer;

interface Question
{
    /**
     * @param Question $other
     * @return boolean
     */
    public function equals(Question $other);

    /**
     * @return string $question
     */
    public function getQuestion();

    /**
     * @return Answer $answer
     */
    public function getDefaultAnswer();
}
