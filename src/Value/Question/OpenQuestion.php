<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\Answer;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;

final class OpenQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var SingleAnswer
     */
    private $defaultAnswer;

    public function __construct($question, SingleAnswer $defaultAnswer)
    {
        Assertion::string($question);

        $this->question = $question;
        $this->defaultAnswer = $defaultAnswer;
    }

    public function equals(Question $other)
    {
        if (!$other instanceof $this) {
            return false;
        }

        return $this->question === $other->question && $this->defaultAnswer->equals($other->defaultAnswer);
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }
}
