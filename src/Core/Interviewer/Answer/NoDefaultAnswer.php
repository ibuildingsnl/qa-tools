<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

final class NoDefaultAnswer implements Answer
{
    public function getRaw()
    {
        return null;
    }

    public function equals(Answer $other)
    {
        return $other instanceof NoDefaultAnswer;
    }

    public function convertToString()
    {
        return '';
    }
}
