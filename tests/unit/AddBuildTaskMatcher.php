<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Target;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Task\AddBuildTask;
use Ibuildings\QaTools\Core\Task\Task;
use Mockery;

final class AddBuildTaskMatcher
{
    /**
     * @param Target  $target
     * @param Tool    $tool
     * @param Snippet $snippet
     * @return Mockery\Matcher\Closure
     */
    public static function with(Target $target, Tool $tool, Snippet $snippet)
    {
        return Mockery::on(
            function (Task $task) use ($target, $tool, $snippet) {
                return $task instanceof AddBuildTask
                && $task->getTool()->equals($tool)
                && $task->getSnippet()->equals($snippet)
                && $task->getTarget()->equals($target);
            }
        );
    }
}
