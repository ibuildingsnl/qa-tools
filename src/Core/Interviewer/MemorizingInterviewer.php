<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;

final class MemorizingInterviewer implements InterviewerInterface
{
    /**
     * @var Answer[]
     */
    private $previousAnswers;

    /**
     * @var Answer[]
     */
    private $givenAnswers = [];

    /**
     * @var InterviewerInterface
     */
    private $interviewer;

    /**
     * @var string
     */
    private $scope = '';

    public function __construct(array $previousAnswers, InterviewerInterface $interviewer)
    {
        $this->previousAnswers = $previousAnswers;
        $this->interviewer     = $interviewer;
    }

    /**
     * @param string $scope
     */
    public function setScope($scope)
    {
        Assertion::string($scope);
        $this->scope = $scope;
    }

    /**
     * @param Question $question
     * @return boolean
     */
    public function hasPreviousAnswerFor(Question $question)
    {
        return isset($this->previousAnswers[md5($question . $this->scope)]);
    }

    /**
     * @param Question $question
     * @return Answer
     */
    public function getPreviousAnswerFor(Question $question)
    {
        if (!$this->hasPreviousAnswerFor($question)) {
            throw new LogicException(sprintf(
                'Cannot get previous answer for question "%s" and scope "%s": no answer given',
                $question,
                $this->scope
            ));
        }

        return $this->previousAnswers[md5($question . $this->scope)];
    }

    /**
     * @param Question $question
     * @return Answer
     */
    public function hasAnswerFor(Question $question)
    {
        return isset($this->givenAnswers[md5($question . $this->scope)]);
    }

    /**
     * @param Question $question
     * @return Answer
     */
    public function getAnswerFor(Question $question)
    {
        if (!$this->hasAnswerFor($question)) {
            throw new LogicException(sprintf(
                'Cannot get previous answer for question "%s" and scope "%s": no answer given',
                $question,
                $this->scope
            ));
        }

        return $this->givenAnswers[md5($question . $this->scope)];
    }

    /**
     * @param Question $question
     * @return Answer
     */
    public function ask(Question $question)
    {
        if ($this->hasPreviousAnswerFor($question)) {
            $answer = $this->getPreviousAnswerFor($question);
            $question = $question->withDefaultAnswer($answer);
        }

        $givenAnswer = $this->interviewer->ask($question);

        $this->givenAnswers[md5($question . $this->scope)] = $givenAnswer;

        return $givenAnswer;
    }

    /**
     * @param Sentence $sentence
     */
    public function say(Sentence $sentence)
    {
        $this->interviewer->say($sentence);
    }

    /**
     * @param Sentence $sentence
     */
    public function error(Sentence $sentence)
    {
        $this->interviewer->error($sentence);
    }
}
