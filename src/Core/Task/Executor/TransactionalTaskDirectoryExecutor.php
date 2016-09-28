<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Exception;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

/**
 * Executes the tasks stored in the task directory, attempting to roll back all
 * changes in case of a mid-transactional error.
 */
final class TransactionalTaskDirectoryExecutor implements TaskDirectoryExecutor
{
    /**
     * @var Executor[]
     */
    private $executors;

    /**
     * @param Executor[] $executors
     */
    public function __construct(array $executors)
    {
        Assertion::allIsInstanceOf(
            $executors,
            Executor::class,
            'Executor ought to be an instance of Executor, got "%s" of type "%s"'
        );

        $this->executors = $executors;
    }

    public function execute(TaskDirectory $taskDirectory, ScopedInterviewer $interviewer)
    {
        $project = $taskDirectory->getProject();

        $allPrerequisitesAreMet = true;
        foreach ($this->executors as $executor) {
            $interviewer->setScope(get_class($executor));
            $prerequisitesAreMet = $executor->arePrerequisitesMet(
                $taskDirectory->filterTasks([$executor, 'supports']),
                $project,
                $interviewer
            );
            $allPrerequisitesAreMet = $allPrerequisitesAreMet && $prerequisitesAreMet;
        }

        if (!$allPrerequisitesAreMet) {
            $interviewer->say('Not all prerequisites have been met, aborting...');

            throw new RuntimeException('Not all prerequisites have been met');
        }

        $executorsToRollBack = [];
        try {
            foreach ($this->executors as $executor) {
                array_unshift($executorsToRollBack, $executor);
                $interviewer->setScope(get_class($executor));
                $executor->execute($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
            }
            foreach ($this->executors as $executor) {
                $interviewer->setScope(get_class($executor));
                $executor->cleanUp($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
            }
        } catch (Exception $e) {
            $interviewer->say(sprintf('Task execution failed: %s', $e->getMessage()));
            $interviewer->say('Rolling back changes...');

            while (count($executorsToRollBack) > 0) {
                /** @var Executor $executor */
                $executor = array_shift($executorsToRollBack);
                $interviewer->setScope(get_class($executor));
                $executor->rollBack($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
            }

            throw $e;
        }
    }
}
