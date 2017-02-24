<?php

namespace Ibuildings\QaTools\UnitTest\Core\GitHook;

use Ibuildings\QaTools\Core\GitHook\GitHookInstaller;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Mockery;

class GitHookInstallerTest extends MockeryTestCase
{
    const DIRECTORY = '/var/project/';

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

        $this->fileHandler->shouldReceive('exists')->with(self::DIRECTORY . GitHookInstaller::PRE_COMMIT_PATH)->andReturn(false);
        $this->fileHandler->shouldReceive('readFrom')->andReturn('ant precommit');

        $installer->installPreCommitHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldHaveReceived('writeTo', [self::DIRECTORY . GitHookInstaller::PRE_COMMIT_PATH, '/ant precommit/']);
        $this->fileHandler->shouldHaveReceived('changeMode', [self::DIRECTORY . GitHookInstaller::PRE_COMMIT_PATH, 0775]);
    }

    /**
     * @test
     */
    public function should_not_overwrite_pre_commit_hook_if_user_refuses()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $this->fileHandler->shouldReceive('exists')->with(self::DIRECTORY . GitHookInstaller::PRE_COMMIT_PATH)->andReturn(true);

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

        $this->fileHandler->shouldReceive('exists')->with(self::DIRECTORY . GitHookInstaller::PRE_COMMIT_PATH)->andReturn(true);

        $this->interviewer->recordAnswer(
            'A pre-commit hook already exists in this project. Are you sure you want to overwrite it?',
            YesOrNoAnswer::yes()
        );

        $this->fileHandler->shouldReceive('readFrom')->andReturn('ant precommit');

        $installer->installPreCommitHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldHaveReceived('writeTo', [self::DIRECTORY . GitHookInstaller::PRE_COMMIT_PATH, '/ant precommit/']);
        $this->fileHandler->shouldHaveReceived('changeMode', [self::DIRECTORY . GitHookInstaller::PRE_COMMIT_PATH, 0775]);
    }

    /**
     * @test
     */
    public function installs_pre_push_hook()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $this->fileHandler->shouldReceive('exists')->with(self::DIRECTORY.GitHookInstaller::PRE_PUSH_PATH)->andReturn(false);
        $this->fileHandler->shouldReceive('readFrom')->andReturn('ant prepush');

        $installer->installPrePushHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldHaveReceived('writeTo', [self::DIRECTORY.GitHookInstaller::PRE_PUSH_PATH, '/ant prepush/']);
        $this->fileHandler->shouldHaveReceived('changeMode', [self::DIRECTORY.GitHookInstaller::PRE_PUSH_PATH, 0775]);
    }

    /**
     * @test
     */
    public function should_not_overwrite_pre_push_hook_if_user_refuses()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $this->fileHandler->shouldReceive('exists')->with(self::DIRECTORY.GitHookInstaller::PRE_PUSH_PATH)->andReturn(true);

        $this->interviewer->recordAnswer(
            'A pre-push hook already exists in this project. Are you sure you want to overwrite it?',
            YesOrNoAnswer::no()
        );

        $installer->installPrePushHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldNotHaveReceived('writeTo');
    }

    /**
     * @test
     */
    public function should_overwrite_pre_push_hook_if_user_confirms()
    {
        $installer = new GitHookInstaller($this->fileHandler);

        $this->fileHandler->shouldReceive('exists')->with(self::DIRECTORY.GitHookInstaller::PRE_PUSH_PATH)->andReturn(true);

        $this->interviewer->recordAnswer(
            'A pre-push hook already exists in this project. Are you sure you want to overwrite it?',
            YesOrNoAnswer::yes()
        );

        $this->fileHandler->shouldReceive('readFrom')->andReturn('ant prepush');

        $installer->installPrePushHook($this->interviewer, $this->directory);

        $this->fileHandler->shouldHaveReceived('writeTo', [self::DIRECTORY.GitHookInstaller::PRE_PUSH_PATH, '/ant prepush/']);
        $this->fileHandler->shouldHaveReceived('changeMode', [self::DIRECTORY.GitHookInstaller::PRE_PUSH_PATH, 0775]);
    }
}
