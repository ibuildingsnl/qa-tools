<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\TextualAnswer;

final class TextualQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var TextualAnswer
     */
    private $defaultAnswer;

    public function __construct($question, TextualAnswer $defaultAnswer)
    {
        Assertion::string($question);

        $this->question      = $question;
        $this->defaultAnswer = $defaultAnswer;
    }

    /**
     * @param TextualQuestion $other
     * @return bool
     */
    public function equals(TextualQuestion $other)
    {
        return $this->question === $other->question && $this->defaultAnswer->equals($other->defaultAnswer);
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @return TextualAnswer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }

    /**
     * @return string
     */
    public function getDefaultAnswerAsString()
    {
        return $this->getDefaultAnswer()->getAnswer();
    }
}
