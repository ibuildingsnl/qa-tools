<?php

namespace Ibuildings\QaTools\UnitTest\Core\GitHook;

use Ibuildings\QaTools\Core\GitHook\GitHookInstaller;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Mockery;
use PHPUnit_Framework_TestCase;

class GitHookInstallerTest extends PHPUnit_Framework_TestCase
{
    const DIRECTORY = '/var/project/';

    const PRE_COMMIT_HOOK_FILE = '/var/project/.git/hooks/pre-commit';

    /**
     * @var Mockery\MockInterface|Directory
     */
    private $directory;

    /**
     * @var Mockery\MockInterface|FileHandler
     */
    private $fileHandler;

    protected function setUp()
    {
        $this->directory = new Directory(self::DIRECTORY);
        $this->fileHandler = Mockery::spy(FileHandler::class);
    }

    /**
     * @test
     */
    public function installs_git_hook()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $installer->installPreCommitHook($this->directory);

        $this->fileHandler->shouldHaveReceived('writeTo', [self::PRE_COMMIT_HOOK_FILE, '/ant precommit/']);
        $this->fileHandler->shouldHaveReceived('changeMode', [self::PRE_COMMIT_HOOK_FILE, 0775]);
    }
}
