<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Mockery;

final class InstallComposerDevDependencyTaskMatcher
{
    /**
     * @param string $expected
     * @return Mockery\Matcher\Closure
     */
    public static function forAnyVersionOf($expected)
    {
        return Mockery::on(
            function (Task $task) use ($expected) {
                return $task instanceof InstallComposerDevDependencyTask
                    && $task->getPackageName() === $expected;
            }
        );
    }

    /**
     * @param string $expectedPackage
     * @param string $expectedVersion
     * @return Mockery\Matcher\Closure
     */
    public static function forVersionOf($expectedPackage, $expectedVersion)
    {
        return Mockery::on(
            function (Task $task) use ($expectedPackage, $expectedVersion) {
                return $task instanceof InstallComposerDevDependencyTask
                    && $task->getPackageName() === $expectedPackage
                    && $task->getPackageVersionConstraint() == $expectedVersion;
            }
        );
    }
}
