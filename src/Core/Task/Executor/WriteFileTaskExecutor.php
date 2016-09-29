<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;
use Ibuildings\QaTools\Core\Task\WriteFileTask;

final class WriteFileTaskExecutor implements Executor
{
    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var string[]
     */
    private $filesWritten;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
        $this->filesWritten = [];
    }

    public function supports(Task $task)
    {
        return $task instanceof WriteFileTask;
    }

    public function arePrerequisitesMet(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $canWriteAllFiles = true;
        foreach ($tasks as $task) {
            /** @var WriteFileTask $task */
            if (!$this->fileHandler->canWriteWithBackupTo($task->getFilePath())) {
                $interviewer->warn(sprintf('Cannot write file "%s"; is the directory writable?', $task->getFilePath()));
                $canWriteAllFiles = false;
            }
        }

        return $canWriteAllFiles;
    }

    public function execute(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $this->filesWritten = [];

        foreach ($tasks as $task) {
            /** @var WriteFileTask $task */
            $this->fileHandler->writeWithBackupTo($task->getFilePath(), $task->getFileContents());
            array_unshift($this->filesWritten, $task->getFilePath());
        }
    }

    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        while (count($this->filesWritten) > 0) {
            $this->fileHandler->discardBackupOf(array_shift($this->filesWritten));
        }
    }

    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        while (count($this->filesWritten) > 0) {
            $this->fileHandler->restoreBackupOf(array_shift($this->filesWritten));
        }
    }
}
