<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Exception;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

final class CautiousExecutorExecutor implements ExecutorExecutor
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

        foreach ($this->executors as $executor) {
            $interviewer->setScope(get_class($executor));
            $executor->checkPrerequisites(
                $taskDirectory->filterTasks([$executor, 'supports']),
                $project,
                $interviewer
            );
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
