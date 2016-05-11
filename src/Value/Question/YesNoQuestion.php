<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\Answer;
use Ibuildings\QaTools\Value\Answer\YesNoAnswer;

final class YesNoQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var YesNoAnswer
     */
    private $defaultAnswer;

    public function __construct($question, YesNoAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer= YesNoAnswer::yes();
        }

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

    /**
     * @return string $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @return Answer $answer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }
}
