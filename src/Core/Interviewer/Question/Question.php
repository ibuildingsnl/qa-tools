<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

interface Question
{
    /**
     * @param mixed $answer
     * @return static
     */
    public function withDefaultAnswer($answer);

    public function __toString();
}
