<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question as QuestionInterface;
use Ibuildings\QaTools\Core\Project\ProjectConfigurator;

final class MemorizingInterviewer implements Interviewer
{
    /**
     * @var Interviewer
     */
    private $interviewer;

    /**
     * @var Answer[]
     */
    private $previousAnswers;

    /**
     * @var Answer[]
     */
    private $givenAnswers = [];

    /**
     * @var string
     */
    private $scope = ProjectConfigurator::class;

    public function __construct(Interviewer $interviewer, array $previousAnswers)
    {
        Assertion::allIsInstanceOf($previousAnswers, Answer::class);
        Assertion::allString(array_keys($previousAnswers), 'Answers key "%s" was expected to be a hash, type %s given.');

        $this->interviewer  = $interviewer;
        $this->previousAnswers = $previousAnswers;
    }

    /**
     * @param string $scope
     */
    public function setScope($scope)
    {
        Assertion::string($scope);

        $this->scope = $scope;
    }

    public function ask(QuestionInterface $question)
    {
        $questionIdentifier = md5($question.$this->scope);

        if (isset($this->previousAnswers[$questionIdentifier])) {
            $answer   = $this->previousAnswers[$questionIdentifier];
            $question = $question->withDefaultAnswer($answer);
        }

        $givenAnswer = $this->interviewer->ask($question);

        $this->givenAnswers[$questionIdentifier] = $givenAnswer;

        return $givenAnswer;
    }

    public function say($sentence)
    {
        $this->interviewer->say($sentence);
    }

    public function warn($sentence)
    {
        $this->interviewer->warn($sentence);
    }

    /**
     * @return Answer[]
     */
    public function getGivenAnswers()
    {
        return $this->givenAnswers;
    }
}
