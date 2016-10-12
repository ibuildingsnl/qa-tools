<?php
namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Build\Target;
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
     * @var array
     */
    private $toolPriorities;

    /**
     * @param FileHandler    $fileHandler
     * @param TemplateEngine $templateEngine
     * @param string         $templatesLocation
     * @param array          $toolPriorities
     */
    public function __construct(
        FileHandler $fileHandler,
        TemplateEngine $templateEngine,
        $templatesLocation,
        array $toolPriorities = []
    ) {
        $this->fileHandler = $fileHandler;
        $this->templateEngine = $templateEngine;
        $this->templatesLocation = $templatesLocation;
        $this->toolPriorities = ['phplint', 'phpmd', 'phpcs']; //$toolPriorities;
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
                self::getStageSnippetsAndTargets($tasks, Target::build(), $this->toolPriorities),
                self::getStageSnippetsAndTargets($tasks, Target::preCommit(), $this->toolPriorities)
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
     * @param Target   $target
     * @param array    $toolPriorities
     * @return array
     */
    private static function getStageSnippetsAndTargets(TaskList $tasks, Target $target, array $toolPriorities)
    {
        $buildStage = $tasks->filter(function (AddBuildTask $task) use ($target) {
            return $task->getTarget()->equals($target);
        });

        $prioritizedTasksForStage = $buildStage->sort(
            function (AddBuildTask $first, AddBuildTask $second) use ($toolPriorities) {
                return $first->getTool()->compare($second->getTool(), $toolPriorities);
            }
        );

        $snippetsForStage = $prioritizedTasksForStage->reduce(
            function ($carry, AddBuildTask $task) {
                return $carry . $task->getSnippet()->getContents() . "\n";
            }
        );

        $targetsForStage = $prioritizedTasksForStage->map(
            function (AddBuildTask $task) {
                return $task->getSnippet()->getTarget();
            }
        );

        return array(
            "{$target->getTargetIdentifier()}_snippets" => $snippetsForStage,
            "{$target->getTargetIdentifier()}_targets" => $targetsForStage
        );
    }
}
