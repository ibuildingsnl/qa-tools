<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Stages\Stage;
use Ibuildings\QaTools\Core\Task\AddBuildTask;
use Ibuildings\QaTools\Core\Task\Task;
use Mockery;

final class AddBuildTaskMatcher
{
    /**
     * @param Stage $expectedStage
     * @param string $expectedTemplate
     * @param string $expectedTargetName
     * @return Mockery\Matcher\Closure
     */
    public static function forStage(Stage $expectedStage, $expectedTemplate, $expectedTargetName)
    {
        return Mockery::on(
            function (Task $task) use ($expectedStage, $expectedTemplate, $expectedTargetName) {
                return $task instanceof AddBuildTask
                && $task->getStage() == $expectedStage
                && $task->getTemplate() === $expectedTemplate
                && $task->getTargetName() === $expectedTargetName;
            }
        );
    }
}
