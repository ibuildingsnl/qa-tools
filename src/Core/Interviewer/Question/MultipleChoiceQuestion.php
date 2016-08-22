<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use LogicException;

/**
 * Describes a multiple-choice question, where one is to give a single answer. The
 * list-choice question is similar, but allows one to give multiple answers.
 */
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
     * @var TextualAnswer|NoDefaultAnswer
     */
    private $defaultAnswer;

    public function __construct($question, Choices $possibleAnswers, TextualAnswer $defaultAnswer = null)
    {
        Assertion::string($question);

        if ($defaultAnswer === null) {
            $defaultAnswer = new NoDefaultAnswer();
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
        return new MultipleChoiceQuestion($this->question, $this->possibleChoices, $answer);
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
     * @param Choices $possibleAnswers
     * @param TextualAnswer $defaultAnswer
     */
    private function assertDefaultAnswerIsPossible(Choices $possibleAnswers, TextualAnswer $defaultAnswer)
    {
        if (!$possibleAnswers->contain($defaultAnswer)) {
            throw new LogicException(
                sprintf(
                    'Cannot create question: default answer "%s" is not a possible answer',
                    $defaultAnswer->getRaw()
                )
            );
        }
    }
}
