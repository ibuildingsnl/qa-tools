<?php

namespace Ibuildings\QaTools\Value\Answer;

use Ibuildings\QaTools\Assert\Assertion;

final class SingleAnswer implements Answer
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
