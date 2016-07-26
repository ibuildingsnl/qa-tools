<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Project\Project;

class Configuration
{
    /**
     * @var Project $project
     */
    private $project;

    /**
     * @var Answer[]
     */
    private $answers;

    /**
     * @return Configuration
     */
    public static function create()
    {
        return new Configuration();
    }

    public static function loaded(Project $project, array $answers)
    {
        return new Configuration($project, $answers);
    }

    private function __construct(Project $project = null, array $answers = [])
    {
        Assertion::allIsInstanceOf($answers, Answer::class);
        Assertion::allString(array_keys($answers), 'Answer "%s" expected to be a hash, type %s given.');

        $this->project = $project;
        $this->answers = $answers;
    }

    public function reconfigureProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @param string $questionId
     * @param Answer $answer
     */
    public function answer($questionId, Answer $answer)
    {
        Assertion::string($questionId, 'Question ID expected to be a hash, got "%s" of type "%s"');

        $this->answers[$questionId] = $answer;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param string $questionId
     * @return bool
     */
    public function hasAnswer($questionId)
    {
        Assertion::string($questionId, 'Question ID expected to be a hash, got "%s" of type "%s"');

        return isset($this->answers[$questionId]);
    }

    /**
     * @param string $questionId
     * @return Answer
     */
    public function getAnswer($questionId)
    {
        if (!$this->hasAnswer($questionId)) {
            throw new RuntimeException(sprintf('No answer with id "%s" stored in configuration', $questionId));
        }

        return $this->answers[$questionId];
    }

    /**
     * @return Answer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
