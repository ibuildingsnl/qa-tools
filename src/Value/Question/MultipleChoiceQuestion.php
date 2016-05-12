<?php

namespace Ibuildings\QaTools\Value\Question;

use Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\Choices;
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
     * @var TextualAnswer
     */
    private $defaultAnswer;

    public function __construct($question, Choices $possibleAnswers, TextualAnswer $defaultAnswer)
    {
        Assertion::string($question);

        if (!$possibleAnswers->contain($defaultAnswer)) {
            throw new LogicException(
                sprintf(
                    'Cannot create question: default answer "%s" is not a possible answer',
                    $defaultAnswer->getAnswer()
                )
            );
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
    public function getPossibleChoicesAsStrings()
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

    public function getDefaultAnswerAsString()
    {
        return $this->defaultAnswer->getAnswer();
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
}
