<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Mockery;


final class WriteFileTaskMatcher
{
    /**
     * @param string $expectedPath
     * @param string $expectedContent
     * @return Mockery\Matcher\Closure
     */
    public static function contains($expectedPath, $expectedContent)
    {
        return Mockery::on(
            function (Task $task) use ($expectedPath, $expectedContent) {
                return $task instanceof WriteFileTask
                    && $task->getFilePath() === $expectedPath
                    && $task->getFileContents() === $expectedContent;
            }
        );
    }
}
