<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

final class MissingAnswer implements Answer
{
    public function getAnswer()
    {
        return null;
    }

    public function equals(Answer $other)
    {
        return $other instanceof MissingAnswer;
    }

    public function convertToString()
    {
        return null;
    }
}
