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

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) -- I've spent a considerable amount of time attempting to reduce
     *     the complexity; there are lots of little details to manage and, for now, abstracting things won't make the
     *     process easier to grok.
     * @param TaskDirectory     $taskDirectory
     * @param ScopedInterviewer $interviewer
     * @return bool
     * @throws Exception
     */
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

        Sigints::trap();
        $executorsToRollBack = [];
        try {
            foreach ($executorsWithTasks as $executor) {
                if (Sigints::wereTrapped()) {
                    break;
                }

                array_unshift($executorsToRollBack, $executor);
                $interviewer->setScope(get_class($executor));
                $executor->execute($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
            }

            foreach ($executorsWithTasks as $executor) {
                if (Sigints::wereTrapped()) {
                    break;
                }

                $interviewer->setScope(get_class($executor));
                $executor->cleanUp($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
            }
        } catch (Exception $e) {
            Sigints::resetTrap();

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

        if (Sigints::wereTrapped()) {
            Sigints::resetTrap();
            $interviewer->notice('Received SIGINT; rolling back changes...');

            while (count($executorsToRollBack) > 0) {
                /** @var Executor $executor */
                $executor = array_shift($executorsToRollBack);
                $interviewer->setScope(get_class($executor));
                $executor->rollBack($taskDirectory->filterTasks([$executor, 'supports']), $project, $interviewer);
            }
        }

        $interviewer->success('');
        $interviewer->success('Done!');
        $interviewer->success('');

        return true;
    }
}
