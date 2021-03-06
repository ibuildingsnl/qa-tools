<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Build\Build;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Task\AddAntBuildTask;
use Ibuildings\QaTools\Core\Task\Task;
use Mockery;

final class AddBuildTaskMatcher
{
    /**
     * @param Build   $target
     * @param Tool    $tool
     * @param Snippet $snippet
     * @return Mockery\Matcher\Closure
     */
    public static function with(Build $target, Tool $tool, Snippet $snippet)
    {
        return Mockery::on(
            function (Task $task) use ($target, $tool, $snippet) {
                if ($task instanceof AddAntBuildTask) {
                    $other = new AddAntBuildTask($target, $tool, $snippet);
                    return $task->equals($other);
                }
                return false;
            }
        );
    }
}
