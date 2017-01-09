<?php

namespace Ibuildings\QaTools\Core\GitHook;

use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Directory;

final class GitHookInstaller
{
    const PRE_COMMIT_PATH = '.git/hooks/pre-commit';

    /**
     * @var FileHandler
     */
    private $fileHandler;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    public function installPreCommitHook(Directory $projectRoot)
    {
        $filePath = $projectRoot->getDirectory().self::PRE_COMMIT_PATH;

        $this->fileHandler->writeTo($filePath, file_get_contents(__DIR__.'/files/pre-commit'));
        $this->fileHandler->changeMode($filePath, 0775);
    }
}
