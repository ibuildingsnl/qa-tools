<?php

namespace Ibuildings\QaTools\Value\Answer;

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
}
