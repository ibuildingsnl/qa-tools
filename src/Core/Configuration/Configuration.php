<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Project\Project;

final class Configuration
{
    /**
     * @var Project $project
     */
    private $project;

    /**
     * @var Answer[]
     */
    private $answers;

    public function __construct(Project $project, array $answers = [])
    {
        Assertion::allIsInstanceOf($answers, Answer::class);
        Assertion::allString(array_keys($answers), 'Answer "%s" expected to be a hash, type %s given.');

        $this->project = $project;
        $this->answers = $answers;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return Answer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
