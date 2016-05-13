<?php

namespace Ibuildings\QaTools\Value\Question;

use Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\Choices;
use Ibuildings\QaTools\Value\Answer\MissingAnswer;
use Ibuildings\QaTools\Value\Answer\TextualAnswer;
use LogicException;

final class MultipleChoiceQuestion implements Question
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
     * @var TextualAnswer|MissingAnswer
     */
    private $defaultAnswer;

    public function __construct($question, Choices $possibleAnswers, TextualAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer = new MissingAnswer();
        } else {
            $this->assertDefaultAnswerIsPossible($possibleAnswers, $defaultAnswer);
        }

        $this->question        = $question;
        $this->possibleChoices = $possibleAnswers;
        $this->defaultAnswer   = $defaultAnswer;
    }

    public function equals(MultipleChoiceQuestion $other)
    {
        return $this->question === $other->question
        && $this->defaultAnswer->equals($other->defaultAnswer)
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
            iterator_to_array(
                $this->getPossibleChoices()
            )
        );
    }

    /**
     * @return string
     */
    public function getDefaultAnswerValue()
    {
        return $this->defaultAnswer->getAnswer();
    }

    public function hasDefaultAnswer()
    {
        return !$this->defaultAnswer instanceof MissingAnswer;
    }

    /**
     * @return string $question
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
     * @return TextualAnswer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }

    /**
     * @param Choices $possibleAnswers
     * @param TextualAnswer $defaultAnswer
     */
    public function assertDefaultAnswerIsPossible(Choices $possibleAnswers, TextualAnswer $defaultAnswer)
    {
        if (!$possibleAnswers->contain($defaultAnswer)) {
            throw new LogicException(
                sprintf(
                    'Cannot create question: default answer "%s" is not a possible answer',
                    $defaultAnswer->getAnswer()
                )
            );
        }
    }
}
