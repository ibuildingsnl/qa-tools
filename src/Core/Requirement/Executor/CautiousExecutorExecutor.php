<?php

namespace Ibuildings\QaTools\Core\Requirement\Executor;

use Exception;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Throwable;

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

    public function execute(RequirementDirectory $requirementDirectory, ScopedInterviewer $interviewer)
    {
        foreach ($this->executors as $executor) {
            $interviewer->setScope(get_class($executor));
            $executor->checkPrerequisites(
                $requirementDirectory->filterRequirements([$executor, 'supports']),
                $interviewer
            );
        }

        $executorsToRollBack = [];
        try {
            foreach ($this->executors as $executor) {
                array_unshift($executorsToRollBack, $executor);
                $interviewer->setScope(get_class($executor));
                $executor->execute($requirementDirectory->filterRequirements([$executor, 'supports']), $interviewer);
            }
            foreach ($this->executors as $executor) {
                $interviewer->setScope(get_class($executor));
                $executor->cleanUp($requirementDirectory->filterRequirements([$executor, 'supports']), $interviewer);
            }
        } catch (Exception $e) {
            while (count($executorsToRollBack) > 0) {
                /** @var Executor $executor */
                $executor = array_shift($executorsToRollBack);
                $interviewer->setScope(get_class($executor));
                $executor->rollBack($requirementDirectory->filterRequirements([$executor, 'supports']), $interviewer);
            }

            throw $e;
        }
    }
}
