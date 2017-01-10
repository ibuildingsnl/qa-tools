<?php

namespace Ibuildings\QaTools\UnitTest\Core\GitHook;

use Ibuildings\QaTools\Core\GitHook\GitHookInstaller;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Directory;
use Mockery;
use PHPUnit_Framework_TestCase;

class GitHookInstallerTest extends PHPUnit_Framework_TestCase
{
    const DIRECTORY = '/var/project/';

    const PRE_COMMIT_HOOK_FILE = self::DIRECTORY.'.git/hooks/pre-commit';

    /**
     * @var Mockery\MockInterface|FileHandler
     */
    private $fileHandler;

    /**
     * @var AutomatedResponseInterviewer
     */
    private $interviewer;

    /**
     * @var Mockery\MockInterface|Directory
     */
    private $directory;

    protected function setUp()
    {
        $this->directory = new Directory(self::DIRECTORY);
        $this->interviewer = new AutomatedResponseInterviewer();
        $this->fileHandler = Mockery::spy(FileHandler::class);
    }

    /**
     * @test
     */
    public function installs_pre_commit_hook()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $this->fileHandler->shouldReceive('exists')->with(self::PRE_COMMIT_HOOK_FILE)->andReturn(false);
        $this->fileHandler->shouldReceive('readFrom')->andReturn('ant precommit');

        $installer->installPreCommitHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldHaveReceived('writeTo', [self::PRE_COMMIT_HOOK_FILE, '/ant precommit/']);
        $this->fileHandler->shouldHaveReceived('changeMode', [self::PRE_COMMIT_HOOK_FILE, 0775]);
    }

    /**
     * @test
     */
    public function should_not_overwrite_pre_commit_hook_if_user_refuses()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $this->fileHandler->shouldReceive('exists')->with(self::PRE_COMMIT_HOOK_FILE)->andReturn(true);

        $this->interviewer->recordAnswer(
            'A pre-commit hook already exists in this project. Are you sure you want to overwrite it?',
            YesOrNoAnswer::no()
        );

        $installer->installPreCommitHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldNotHaveReceived('writeTo');
    }

    /**
     * @test
     */
    public function should_overwrite_pre_commit_hook_if_user_confirms()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $this->fileHandler->shouldReceive('exists')->with(self::PRE_COMMIT_HOOK_FILE)->andReturn(true);

        $this->interviewer->recordAnswer(
            'A pre-commit hook already exists in this project. Are you sure you want to overwrite it?',
            YesOrNoAnswer::yes()
        );

        $this->fileHandler->shouldReceive('readFrom')->andReturn('ant precommit');

        $installer->installPreCommitHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldHaveReceived('writeTo', [self::PRE_COMMIT_HOOK_FILE, '/ant precommit/']);
        $this->fileHandler->shouldHaveReceived('changeMode', [self::PRE_COMMIT_HOOK_FILE, 0775]);
    }
}
