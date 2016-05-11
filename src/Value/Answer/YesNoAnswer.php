<?php

namespace Ibuildings\QaTools\Value\Answer;

use Ibuildings\QaTools\Assert\Assertion;

final class YesNoAnswer implements Answer
{
    /**
     * @var boolean
     */
    private $answer;

    /**
     * @return YesNoAnswer<true>
     */
    public static function yes()
    {
        return new self(true);
    }

    /**
     * @return YesNoAnswer<false>
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

    public function equals(Answer $other)
    {
        if (!$other instanceof $this) {
            return false;
        }

        return $this->answer === $other->answer;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
