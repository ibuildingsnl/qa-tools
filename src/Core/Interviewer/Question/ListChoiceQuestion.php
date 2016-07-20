<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;

final class ListChoiceQuestion implements Question
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
     * @var Choices|NoDefaultAnswer
     */
    private $defaultAnswer;

    public function __construct($question, Choices $possibleChoices, Choices $defaultChoices = null)
    {
        Assertion::string($question);

        if ($defaultChoices === null) {
            $defaultChoices = new NoDefaultAnswer;
        } else {
            $this->assertDefaultChoiceIsPossible($possibleChoices, $defaultChoices);
        }

        $this->question        = $question;
        $this->possibleChoices = $possibleChoices;
        $this->defaultAnswer   = $defaultChoices;
    }

    /**
     * @param ListChoiceQuestion $other
     * @return bool
     */
    public function equals(ListChoiceQuestion $other)
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
     * @return Choices
     */
    public function getPossibleChoices()
    {
        return $this->possibleChoices;
    }

    /**
     * @return Choices|NoDefaultAnswer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }

    /**
     * @param Choices $defaultAnswer
     * @return ListChoiceQuestion
     */
    public function withDefaultAnswer($defaultAnswer)
    {
        return new ListChoiceQuestion($this->question, $this->possibleChoices, $defaultAnswer);
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s(question="%s", choices="%s")',
            self::class,
            $this->question,
            $this->getPossibleChoices()->convertToString()
        );
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
