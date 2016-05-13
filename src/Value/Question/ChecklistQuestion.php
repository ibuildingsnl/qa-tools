<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Exception\LogicException;
use Ibuildings\QaTools\Value\Answer\Choices;
use Ibuildings\QaTools\Value\Answer\MissingAnswer;
use Ibuildings\QaTools\Value\Answer\TextualAnswer;

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
    private $defaultChoices;

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
        $this->defaultChoices  = $defaultChoices;
    }

    /**
     * @param ChecklistQuestion $other
     * @return bool
     */
    public function equals(ChecklistQuestion $other)
    {
        return $this->question === $other->question
        && $this->defaultChoices->equal($other->defaultChoices)
        && $this->possibleChoices->equal($other->possibleChoices);
    }

    /**
     * @return string[]
     */
    public function getPossibleChoiceValues()
    {
        return array_map(
            function (TextualAnswer $answer) {
                return $answer->getAnswer();
            },
            iterator_to_array($this->getPossibleChoices())
        );
    }

    /**
     * @return string|null
     */
    public function getDefaultChoiceValues()
    {
        if (!$this->hasDefaultChoices()) {
            return null;
        }

        return implode(
            ', ',
            array_map(
                function (TextualAnswer $answer) {
                    return $answer->getAnswer();
                },
                iterator_to_array($this->getDefaultChoices())
            )
        );
    }

    /**
     * @return bool
     */
    public function hasDefaultChoices()
    {
        return !$this->defaultChoices instanceof MissingAnswer;
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
    public function getDefaultChoices()
    {
        return $this->defaultChoices;
    }

    /**
     * @param Choices $possibleChoices
     * @param Choices $defaultChoices
     */
    public function assertDefaultChoiceIsPossible(Choices $possibleChoices, Choices $defaultChoices)
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
