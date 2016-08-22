<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Project\Project;

class Configuration
{
    /**
     * @var Project|null $project
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
     * @param QuestionId $questionId
     * @param Answer $answer
     */
    public function answer(QuestionId $questionId, Answer $answer)
    {
        $this->answers[$questionId->getQuestionId()] = $answer;
    }

    /**
     * @return Project|null
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param QuestionId $questionId
     * @return bool
     */
    public function hasAnswer(QuestionId $questionId)
    {
        return isset($this->answers[$questionId->getQuestionId()]);
    }

    /**
     * @param QuestionId $questionId
     * @return Answer
     */
    public function getAnswer(QuestionId $questionId)
    {
        if (!$this->hasAnswer($questionId)) {
            throw new RuntimeException(sprintf('No answer with id "%s" stored in configuration', $questionId));
        }

        return $this->answers[$questionId->getQuestionId()];
    }

    /**
     * @return Answer[]
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
