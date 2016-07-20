<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;

final class TextualQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var TextualAnswer|NoDefaultAnswer
     */
    private $defaultAnswer;

    public function __construct($question, TextualAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer = new NoDefaultAnswer;
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
     * @return boolean
     */
    public function hasDefaultAnswer()
    {
        return !$this->defaultAnswer instanceof NoDefaultAnswer;
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

    /**
     * @param TextualAnswer $defaultAnswer
     * @return TextualQuestion
     */
    public function withDefaultAnswer($defaultAnswer)
    {
        return new TextualQuestion($this->question, $defaultAnswer);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s(question="%s")', self::class, $this->question);
    }
}
