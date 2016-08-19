<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Task\Specification\Specification;
use Ibuildings\QaTools\Core\Task\TaskList;
use Mockery;

final class Tasks
{
    /**
     * @param Specification $specification
     * @return Mockery\Matcher\Closure
     */
    public static function anyAmountOfTasksMatching(Specification $specification)
    {
        return Mockery::on(
            function (TaskList $taskList) use ($specification) {
                Assertion::greaterThan($taskList->match($specification)->count(), 0);

                return true;
            }
        );
    }

    /**
     * @param Specification $specification
     * @return Mockery\Matcher\Closure
     */
    public static function noTasksMatching(Specification $specification)
    {
        return Mockery::on(
            function (TaskList $taskList) use ($specification) {
                Assertion::count($taskList->match($specification), 0);

                return true;
            }
        );
    }
}
