<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\MissingAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;

final class ChecklistQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var Choices
     */
    private $possibleChoices;

    /**
     * @var Choices|MissingAnswer
     */
    private $defaultAnswer;

    public function __construct($question, Choices $possibleChoices, Choices $defaultChoices = null)
    {
        Assertion::string($question);

        if ($defaultChoices === null) {
            $defaultChoices = new MissingAnswer;
        } else {
            $this->assertDefaultChoiceIsPossible($possibleChoices, $defaultChoices);
        }

        $this->question        = $question;
        $this->possibleChoices = $possibleChoices;
        $this->defaultAnswer   = $defaultChoices;
    }

    /**
     * @param ChecklistQuestion $other
     * @return bool
     */
    public function equals(ChecklistQuestion $other)
    {
        return $this->question === $other->question
        && $this->defaultAnswer->equal($other->defaultAnswer)
        && $this->possibleChoices->equal($other->possibleChoices);
    }

    /**
     * @return bool
     */
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
     * @return Choices
     */
    public function getPossibleChoices()
    {
        return $this->possibleChoices;
    }

    /**
     * @return Choices|MissingAnswer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }

    /**
     * @param Choices $defaultAnswer
     * @return ChecklistQuestion
     */
    public function withDefaultAnswer(Choices $defaultAnswer)
    {
        return new ChecklistQuestion($this->question, $this->possibleChoices, $defaultAnswer);
    }

    /**
     * @return string
     */
    public function calculateHash()
    {
        return md5(self::class
            . $this->question
            . $this->getPossibleChoices()->convertToString()
            . $this->getDefaultAnswer()->convertToString()
        );
    }

    public function __toString()
    {
        return $this->question;
    }

    /**
     * @param Choices $possibleChoices
     * @param Choices $defaultChoices
     */
    private function assertDefaultChoiceIsPossible(Choices $possibleChoices, Choices $defaultChoices)
    {
        /** @var TextualAnswer $defaultAnswer */
        foreach ($defaultChoices as $defaultAnswer) {
            if (!$possibleChoices->contain($defaultAnswer)) {
                throw new LogicException(
                    sprintf(
                        'Cannot create question: default answer "%s" is not a possible answer',
                        $defaultAnswer->getAnswer()
                    )
                );
            }
        }
    }
}
