<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Exception;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

/**
 * Executes the tasks stored in the task directory, attempting to roll back all
 * changes in case of a mid-transactional error.
 */
final class TransactionalTaskDirectoryExecutor implements TaskDirectoryExecutor
{
    /**
     * @var ExecutorCollection
     */
    private $executors;

    /**
     * @param ExecutorCollection $executors
     */
    public function __construct(ExecutorCollection $executors)
    {
        $this->executors = $executors;
    }

    public function execute(TaskDirectory $taskDirectory, ScopedInterviewer $interviewer)
    {
        $executorsWithTasks = $this->executors->findExecutorsWithAtLeastOneTaskToExecute($taskDirectory);

        $interviewer->notice('');

        $project = $taskDirectory->getProject();

        $allPrerequisitesAreMet = true;
        /** @var Executor $executor */
        foreach ($executorsWithTasks as $executor) {
            $interviewer->setScope(get_class($executor));
            $prerequisitesAreMet = $executor->arePrerequisitesMet(
                $taskDirectory->filterTasks([$executor, 'supports']),
                $project,
                $interviewer
            );
            $allPrerequisitesAreMet = $allPrerequisitesAreMet && $prerequisitesAreMet;
        }

        if (!$allPrerequisitesAreMet) {
            $interviewer->notice('Not all prerequisites have been met, aborting...');

            return false;
        }

        pcntl_signal(SIGINT, function () {
            throw new \Exception('Received SIGINT signal. Rolling back changes...');
        });

        $executorsToRollBack = [];
        try {
            foreach ($executorsWithTasks as $executor) {
                array_unshift($executorsToRollBack, $executor);
                $interviewer->setScope(get_class($executor));
                $executor->execute($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
                pcntl_signal_dispatch();
            }

            foreach ($executorsWithTasks as $executor) {
                $interviewer->setScope(get_class($executor));
                $executor->cleanUp($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
                pcntl_signal_dispatch();
            }

            pcntl_signal(SIGINT, SIG_DFL);
        } catch (Exception $e) {
            pcntl_signal(SIGINT, SIG_DFL);

            $interviewer->notice(sprintf('Task execution failed: %s', $e->getMessage()));
            $interviewer->notice('Rolling back changes...');

            while (count($executorsToRollBack) > 0) {
                /** @var Executor $executor */
                $executor = array_shift($executorsToRollBack);
                $interviewer->setScope(get_class($executor));
                $executor->rollBack($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
            }

            throw $e;
        }

        $interviewer->success('');
        $interviewer->success('Done!');
        $interviewer->success('');

        return true;
    }
}
