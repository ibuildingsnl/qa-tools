<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\WriteFileTask as OriginalWriteFileTask;
use Mockery;


final class WriteFileTask
{
    /**
     * @param string $expectedPath
     * @param string $expectedContent
     * @return Mockery\Matcher\Closure
     */
    public static function equals($expectedPath, $expectedContent)
    {
        return Mockery::on(
            function (Task $task) use ($expectedPath, $expectedContent) {
                return $task instanceof OriginalWriteFileTask
                    && $task->getFilePath() === $expectedPath
                    && $task->getFileContents() === $expectedContent;
            }
        );
    }
}
