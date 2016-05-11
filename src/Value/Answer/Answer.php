<?php

namespace Ibuildings\QaTools\Value\Answer;

interface Answer
{
    /**
     * @return boolean
     */
    public function equals(Answer $other);

    /**
     * @return string
     */
    public function getAnswer();
}
