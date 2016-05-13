<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Exception\LogicException;
use Ibuildings\QaTools\Value\Answer\MissingAnswer;
use Ibuildings\QaTools\Value\Answer\TextualAnswer;

final class TextualQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var TextualAnswer|MissingAnswer
     */
    private $defaultAnswer;

    public function __construct($question, TextualAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer = new MissingAnswer;
        }


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
    public function getDefaultAnswerValue()
    {
        return $this->getDefaultAnswer()->getAnswer();
    }

    public function hasDefaultAnswer()
    {
        return !$this->defaultAnswer instanceof MissingAnswer;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @return TextualAnswer|null
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }
}
