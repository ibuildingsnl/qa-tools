<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;

final class YesOrNoQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var YesOrNoAnswer|NoDefaultAnswer
     */
    private $defaultAnswer;

    public function __construct($question, YesOrNoAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer = new NoDefaultAnswer();
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
        return !$this->defaultAnswer instanceof NoDefaultAnswer;
    }

    public function withDefaultAnswer(Answer $answer)
    {
        return new YesOrNoQuestion($this->question, $answer);
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s(question="%s")', self::class, $this->question);
    }
}
