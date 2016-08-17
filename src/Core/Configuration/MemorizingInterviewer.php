<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question as QuestionInterface;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

final class MemorizingInterviewer implements ScopedInterviewer
{
    /**
     * @var Interviewer
     */
    private $interviewer;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $scope = ProjectConfigurator::class;

    /**
     * @param Interviewer   $interviewer
     * @param Configuration $configuration
     */
    public function __construct(Interviewer $interviewer, Configuration $configuration)
    {
        $this->interviewer  = $interviewer;
        $this->configuration = $configuration;
    }

    public function setScope($scope)
    {
        Assertion::string($scope);

        $this->scope = $scope;
    }

    public function ask(QuestionInterface $question)
    {
        $questionIdentifier = QuestionId::fromScopeAndQuestion($this->scope, $question);

        if ($this->configuration->hasAnswer($questionIdentifier)) {
            $previousAnswer = $this->configuration->getAnswer($questionIdentifier);
            $question = $question->withDefaultAnswer($previousAnswer);
        }

        $givenAnswer = $this->interviewer->ask($question);
        $this->configuration->answer($questionIdentifier, $givenAnswer);

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
}
