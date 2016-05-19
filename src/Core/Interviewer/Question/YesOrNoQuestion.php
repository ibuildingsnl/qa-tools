<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\MissingAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;

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
     * @param YesOrNoAnswer $defaultAnswer
     * @return YesOrNoQuestion
     */
    public function withDefaultAnswer(YesOrNoAnswer $defaultAnswer)
    {
        return new YesOrNoQuestion($this->question, $defaultAnswer);
    }

    /**
     * @return string
     */
    public function calculateHash()
    {
        return md5(self::class . $this->question . $this->getDefaultAnswer()->serialize());
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

    public function __toString()
    {
        return $this->question;
    }
}
