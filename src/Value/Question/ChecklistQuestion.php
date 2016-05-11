<?php

namespace Ibuildings\QaTools\Value\Question;

use Ibuildings\QaTools\Assert\Assertion;
use Ibuildings\QaTools\Exception\LogicException;
use Ibuildings\QaTools\Value\Answer\Answer;
use Ibuildings\QaTools\Value\Answer\MultipleAnswers;

final class ChecklistQuestion implements Question
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
     * @var MultipleAnswers
     */
    private $defaultAnswer;

    public function __construct($question, MultipleAnswers $possibleAnswers, MultipleAnswers $defaultAnswers)
    {
        Assertion::string($question);

        /** @var Answer $defaultAnswer */
        foreach($defaultAnswers as $defaultAnswer) {
           if (!$possibleAnswers->contains($defaultAnswer)) {
               throw new LogicException(
                   sprintf(
                       'Cannot create question: default answer "%s" is not a possible answer',
                       $defaultAnswer->getAnswer()
                   )
               );
           }
        }

        $this->question = $question;
        $this->possibleAnswers = $possibleAnswers;
        $this->defaultAnswer = $defaultAnswers;
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
     * @return MultipleAnswers
     */
    public function getDefaultAnswer()
    {
        return $this->defaultAnswer;
    }
}
