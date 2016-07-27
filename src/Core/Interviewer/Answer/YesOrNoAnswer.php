<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class YesOrNoAnswer implements Answer
{
    const YES = true;
    const NO  = false;

    /**
     * @var boolean
     */
    private $answer;

    /**
     * @return YesOrNoAnswer<true>
     */
    public static function yes()
    {
        return new self(self::YES);
    }

    /**
     * @return YesOrNoAnswer<false>
     */
    public static function no()
    {
        return new self(self::NO);
    }

    private function __construct($answer)
    {
        Assertion::boolean($answer);

        $this->answer = $answer;
    }

    /**
     * @param Answer $other
     * @return bool
     */
    public function equals(Answer $other)
    {
        return $other instanceof self && $this->answer === $other->answer;
    }

    /**
     * @return string
     */
    public function getRaw()
    {
        return $this->answer;
    }

    /**
     * @param $yesOrNo
     * @return boolean
     */
    public function is($yesOrNo)
    {
        Assertion::boolean($yesOrNo);

        return $this->answer === $yesOrNo;
    }
}
