<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;

/**
 * Describes a list-choice question, where can give multiple answers. The
 * multiple-choice question is similar, but requires one to give a single answer.
 */
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
        && $this->defaultAnswer->equals($other->defaultAnswer)
        && $this->possibleChoices->equals($other->possibleChoices);
    }

    public function hasDefaultAnswer()
    {
        return !$this->defaultAnswer instanceof NoDefaultAnswer;
    }

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

    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }

    public function withDefaultAnswer(Answer $answer)
    {
        return new ListChoiceQuestion($this->question, $this->possibleChoices, $answer);
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
                        $defaultAnswer->getRaw()
                    )
                );
            }
        }
    }
}
