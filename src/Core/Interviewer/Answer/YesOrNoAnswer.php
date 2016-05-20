<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class YesOrNoAnswer implements Answer
{
    /**
     * @var boolean
     */
    private $answer;

    /**
     * @return YesOrNoAnswer<true>
     */
    public static function yes()
    {
        return new self(true);
    }

    /**
     * @return YesOrNoAnswer<false>
     */
    public static function no()
    {
        return new self(false);
    }

    private function __construct($answer)
    {
        Assertion::boolean($answer);
        $this->answer = $answer;
    }

    /**
     * @param YesOrNoAnswer $other
     * @return bool
     */
    public function equals(YesOrNoAnswer $other)
    {
        return $this->answer === $other->answer;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @return bool
     */
    public function isYes()
    {
        return $this->answer === true;
    }

    /**
     * @return bool
     */
    public function isNo()
    {
        return $this->answer === false;
    }
}
