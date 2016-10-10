<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Stages\Build;
use Ibuildings\QaTools\Core\Stages\Precommit;
use Ibuildings\QaTools\Core\Stages\Stage;
use Ibuildings\QaTools\Core\Task\AddBuildTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;

final class AddBuildTaskExecutor implements Executor
{
    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * @var
     */
    private $templatesLocation;


    /**
     * @param FileHandler    $fileHandler
     * @param TemplateEngine $templateEngine
     * @param string         $templatesLocation
     */
    public function __construct(FileHandler $fileHandler, TemplateEngine $templateEngine, $templatesLocation)
    {
        $this->fileHandler = $fileHandler;
        $this->templateEngine = $templateEngine;
        $this->templatesLocation = $templatesLocation;
    }

    public function supports(Task $task)
    {
        return $task instanceof AddBuildTask;
    }

    public function arePrerequisitesMet(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $antFile = $project->getConfigurationFilesLocation()->getDirectory() . '/build.xml';

        if (!$this->fileHandler->canWriteWithBackupTo($antFile)) {
            $interviewer->warn(sprintf('Cannot write file "%s"; is the directory writable?', $antFile));
            return false;
        }

        return true;
    }

    public function execute(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $this->templateEngine->setPath($this->templatesLocation);

        $contents = $this->templateEngine->render(
            "build.xml.twig",
            array_merge(
                self::getStageSnippetsAndTargets($tasks, new Build()),
                self::getStageSnippetsAndTargets($tasks, new Precommit())
            )
        );

        $buildFile = $project->getConfigurationFilesLocation()->getDirectory() . 'build.xml';
        $this->fileHandler->writeWithBackupTo($buildFile, $contents);
    }

    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
    }

    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $this->fileHandler->restoreBackupOf($project->getConfigurationFilesLocation()->getDirectory() . 'build.xml');
    }

    /**
     * @param TaskList $tasks
     * @param Stage $stage
     * @return array
     */
    private static function getStageSnippetsAndTargets(TaskList $tasks, Stage $stage)
    {
        $buildStage = $tasks->filter(function (AddBuildTask $task) use ($stage) {
            return $task->getStage() == $stage;
        });

        $buildSnippets = $buildStage->reduce(function ($carry, AddBuildTask $task) {
            return $carry . $task->getTemplate() . "\n";
        });

        $buildTargets = $buildStage->map(function (AddBuildTask $task) {
            return $task->getTargetName();
        });

        return array(
            "{$stage->identifier()}_snippets" => $buildSnippets,
            "{$stage->identifier()}_targets" => $buildTargets
        );
    }
}
