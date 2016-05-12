<?php

namespace Ibuildings\QaTools\Value\Answer;

use Ibuildings\QaTools\Assert\Assertion;

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
     * @param TextualAnswer $other
     * @return bool
     */
    public function equals(TextualAnswer $other)
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
}
