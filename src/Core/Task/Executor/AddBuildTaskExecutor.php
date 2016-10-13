<?php
namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Build\Target;
use Ibuildings\QaTools\Core\Task\AddAntBuildTask;
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
     * @var string
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
     * @param string[]       $toolPriorities
     */
    public function __construct(
        FileHandler $fileHandler,
        TemplateEngine $templateEngine,
        $templatesLocation,
        array $toolPriorities = []
    ) {
        $this->fileHandler = $fileHandler;
        $this->templateEngine = $templateEngine;

        Assertion::isArray(
            $toolPriorities,
            sprintf('toolPriorities should be array but is %s', gettype($toolPriorities))
        );
        $this->toolPriorities = $toolPriorities;

        Assertion::string(
            $templatesLocation,
            sprintf('templatesLocation should be string but is %s', gettype($templatesLocation))
        );
        $this->templatesLocation = $templatesLocation;
    }

    public function supports(Task $task)
    {
        return $task instanceof AddAntBuildTask;
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

        $antBuildTargetTasks = self::getTasksOrderedByTool($tasks, Target::build(), $this->toolPriorities);
        $antPrecommitTargetTasks = self::getTasksOrderedByTool($tasks, Target::preCommit(), $this->toolPriorities);

        $buildTargetIdentifier = Target::build()->getTargetIdentifier();
        $precommitTargetIdentifier = Target::preCommit()->getTargetIdentifier();

        $contents = $this->templateEngine->render(
            "build.xml.twig",
            [
                "{$buildTargetIdentifier}_snippets" => self::getSnippets($antBuildTargetTasks),
                "{$buildTargetIdentifier}_targets" => self::getTargets($antBuildTargetTasks),
                "{$precommitTargetIdentifier}_snippets" => self::getSnippets($antPrecommitTargetTasks),
                "{$precommitTargetIdentifier}_targets" => self::getTargets($antPrecommitTargetTasks),
            ]
        );
        $buildFile = $project->getConfigurationFilesLocation()->getDirectory() . 'build.xml';
        $this->fileHandler->writeWithBackupTo($buildFile, $contents);
    }

    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $this->fileHandler->discardBackupOf($project->getConfigurationFilesLocation()->getDirectory() . 'build.xml');
    }

    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $this->fileHandler->restoreBackupOf($project->getConfigurationFilesLocation()->getDirectory() . 'build.xml');
    }

    private static function getSnippets(TaskList $taskList)
    {
        return $taskList->map(
            function (AddAntBuildTask $task) {
                return $task->getSnippetContents();
            }
        );
    }

    private static function getTargets(TaskList $taskList)
    {
        return $taskList->map(
            function (AddAntBuildTask $task) {
                return $task->getSnippetTargetIdentifier();
            }
        );
    }

    private static function getTasksOrderedByTool(TaskList $tasks, Target $target, array $toolPriorities)
    {
        $filteredTasks = $tasks->filter(function (AddAntBuildTask $task) use ($target) {
            return $task->hasTarget($target);
        });

        return $filteredTasks->sort(
            function (AddAntBuildTask $first, AddAntBuildTask $second) use ($toolPriorities) {
                return $first->compare($second, $toolPriorities);
            }
        );
    }
}
