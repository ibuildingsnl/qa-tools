<?php

namespace Ibuildings\QaTools\Core\Interviewer\Answer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class TextualAnswer implements Answer
{
    /**
     * @var string
     */
    private $answer;

    public function __construct($answer)
    {
        Assertion::string($answer);
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
    public function getAnswer()
    {
        return $this->answer;
    }

    public function getRaw()
    {
        return $this->answer;
    }

    /**
     * @return string
     */
    public function convertToString()
    {
        return $this->answer;
    }
}
