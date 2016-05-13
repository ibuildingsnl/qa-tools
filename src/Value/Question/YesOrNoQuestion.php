<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\MissingAnswer;
use Ibuildings\QaTools\Value\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Exception\LogicException;

final class YesOrNoQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var YesOrNoAnswer|MissingAnswer
     */
    private $defaultAnswer;

    public function __construct($question, YesOrNoAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer = new MissingAnswer();
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

    public function hasDefaultAnswer()
    {
        return !$this->defaultAnswer instanceof MissingAnswer;
    }

    /**
     * @return string
     */
    public function getDefaultAnswerAsValue()
    {
        return $this->getDefaultAnswer()->getAnswer();
    }

    /**
     * @return boolean
     */
    public function isDefaultAnswerYes()
    {
        if (!$this->hasDefaultAnswer()) {
            throw new LogicException(
                'Cannot determine if YesNoQuestion has default answer of "yes": no default answer given.'
            );
        }

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
     * @return YesOrNoAnswer|MissingAnswer $answer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }
}
