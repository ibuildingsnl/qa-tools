<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\YesOrNoAnswer;

final class YesOrNoQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var YesOrNoAnswer
     */
    private $defaultAnswer;

    public function __construct($question, YesOrNoAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer = YesOrNoAnswer::yes();
        }

        $this->question      = $question;
        $this->defaultAnswer = $defaultAnswer;
    }

    /**
     * @param YesOrNoQuestion $other
     * @return bool
     */
    public function equals(YesOrNoQuestion $other)
    {
        return $this->question === $other->question && $this->defaultAnswer->equals($other->defaultAnswer);
    }

    /**
     * @return string
     */
    public function getDefaultAnswerAsString()
    {
        return $this->getDefaultAnswer()->getAnswer();
    }

    /**
     * @return bool
     */
    public function defaultAnswerIsYes()
    {
        return $this->getDefaultAnswer()->isYes();
    }

    /**
     * @return string $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @return YesOrNoAnswer $answer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }
}
