<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

interface Answer
{
    /**
     * @param Answer $other
     * @return bool
     */
    public function equals(Answer $other);

    /**
     * @return mixed
     */
    public function getRaw();
}
