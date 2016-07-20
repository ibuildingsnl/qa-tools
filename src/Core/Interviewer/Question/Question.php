<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

interface Question
{
    public function __toString();
    public function withDefaultAnswer($answer);
}
