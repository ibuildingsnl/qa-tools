<?php

namespace Ibuildings\QaTools\Value\Question;

use Assert\Assertion;
use Ibuildings\QaTools\Value\Answer\MultipleAnswers;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use LogicException;

final class MultipleChoiceQuestion implements Question
{
    /**
     * @var string
     */
    private $question;

    /**
     * @var MultipleAnswers
     */
    private $possibleAnswers;

    /**
     * @var SingleAnswer
     */
    private $defaultAnswer;

    public function __construct($question, MultipleAnswers $possibleAnswers, SingleAnswer $defaultAnswer)
    {
        Assertion::string($question);

        if (!$possibleAnswers->contains($defaultAnswer)) {
            throw new LogicException(
                sprintf(
                    'Cannot create question: default answer "%s" is not a possible answer',
                    $defaultAnswer->getAnswer()
                )
            );
        }

        $this->question = $question;
        $this->possibleAnswers = $possibleAnswers;
        $this->defaultAnswer = $defaultAnswer;
    }

    public function equals(Question $other)
    {
        if (!$other instanceof $this) {
            return false;
        }

        return $this->question === $other->question
            && $this->defaultAnswer->equals($other->defaultAnswer)
            && $this->possibleAnswers->equals($other->possibleAnswers);
    }

    /**
     * @return string $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @return MultipleAnswers
     */
    public function getPossibleAnswers()
    {
        return $this->possibleAnswers;
    }

    /**
     * @return SingleAnswer
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }
}
