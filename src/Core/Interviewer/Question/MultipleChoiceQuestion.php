<?php

namespace Ibuildings\QaTools\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\MissingAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
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
     * @return boolean
     */
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
     * @param TextualAnswer $defaultAnswer
     * @return MultipleChoiceQuestion
     */
    public function withDefaultAnswer($defaultAnswer)
    {
        return new MultipleChoiceQuestion($this->question, $this->possibleChoices, $defaultAnswer);
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
                    $defaultAnswer->getAnswer()
                )
            );
        }
    }
}
