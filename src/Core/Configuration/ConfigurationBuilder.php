<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Runner\TaskList;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;

final class ConfigurationBuilder
{
    /**
     * @var Project|null
     */
    private $project;

    /**
     * @var TaskList
     */
    private $taskList;

    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    public function __construct(TemplateEngine $templateEngine, Project $project)
    {
        $this->project        = $project;
        $this->templateEngine = $templateEngine;
        $this->taskList       = new TaskList([]);
    }

    /**
     * @param Task $task
     * @return TaskList
     */
    public function addTask(Task $task)
    {
        $this->taskList = $this->taskList->add($task);
    }

    /**
     * @param string $path
     */
    public function setTemplatePath($path)
    {
        Assertion::nonEmptyString($path, 'path');

        $this->templateEngine->setPath($path);
    }

    public function renderTemplate($template, array $params = [])
    {
        Assertion::nonEmptyString($template, 'template');

        return $this->templateEngine->render($template, $params);
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return TaskList
     */
    public function getTaskList()
    {
        return $this->taskList;
    }
}
