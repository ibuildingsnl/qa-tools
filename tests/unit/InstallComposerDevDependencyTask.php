<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask as OriginalInstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Mockery;

final class InstallComposerDevDependencyTask
{
    /**
     * @param string $expected
     * @return Mockery\Matcher\Closure
     */
    public static function forAnyVersionOf($expected)
    {
        return Mockery::on(
            function (Task $task) use ($expected) {
                return $task instanceof OriginalInstallComposerDevDependencyTask
                    && $task->getPackageName() === $expected;
            }
        );
    }
}
