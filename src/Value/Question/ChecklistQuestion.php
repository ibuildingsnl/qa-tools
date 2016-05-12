<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Exception\LogicException;
use Ibuildings\QaTools\Value\Answer\Choices;
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
     * @var Choices
     */
    private $defaultChoices;

    public function __construct($question, Choices $possibleChoices, Choices $defaultChoices)
    {
        Assertion::string($question);

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
    public function getPossibleChoicesAsStrings()
    {
        return array_map(
            function (TextualAnswer $answer) {
                return $answer->getAnswer();
            },
            iterator_to_array($this->getPossibleChoices())
        );
    }

    /**
     * @return string
     */
    public function getDefaultChoicesAsString()
    {
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
     * @return Choices
     */
    public function getDefaultChoices()
    {
        return $this->defaultChoices;
    }
}
