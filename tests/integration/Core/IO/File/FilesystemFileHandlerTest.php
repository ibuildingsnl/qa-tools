<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\IO\File;

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\IO\File\FilesystemFileHandler;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group IO
 * @group Filesystem
 */
class FilesystemFileHandlerTest extends TestCase
{
    /** @var string */
    private $workingDirectory;
    /** @var FilesystemFileHandler */
    private $adapter;

    protected function setUp()
    {
        $uniqueId = bin2hex(openssl_random_pseudo_bytes(8));
        $this->workingDirectory = sys_get_temp_dir() . '/qa-tools_' . microtime(true)  . '-' . $uniqueId . '_fs-adapter';
        $this->adapter = new FilesystemFileHandler(new Filesystem());
    }

    protected function runTest()
    {
        $oldWd = getcwd();

        try {
            mkdir($this->workingDirectory);
            chdir($this->workingDirectory);
            parent::runTest();
        } finally {
            chdir($oldWd);
        }
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notString
     */
    public function data_to_write_to_file_must_be_a_string($nonString)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->adapter->writeTo('/some/path', $nonString);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function in_order_to_write_given_filepath_must_be_a_string($nonStringOrEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->adapter->writeTo($nonStringOrEmptyString, 'data-to-write');
    }

    /** @test */
    public function throws_an_exception_when_attempting_to_write_to_a_non_writable_directory()
    {
        chmod($this->workingDirectory, 0500);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to write to the "." directory');
        $this->adapter->writeTo('test', 'data');
    }

    /** @test */
    public function attempting_to_read_data_from_a_non_existent_file_fails()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot read from file "does/not/exist" as it does not exist');
        $this->adapter->readFrom('does/not/exist');
    }

    /** @test */
    public function attempting_to_read_data_from_a_file_that_is_not_readable_fails()
    {
        file_put_contents('file', 'data');
        chmod('file', 200);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot read from file "file" as it is not readable');
        $this->adapter->readFrom('file');
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function in_order_to_remove_given_filepath_must_be_a_non_empty_string($filePath)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->adapter->remove($filePath);
    }

    /** @test */
    public function an_exception_thrown_when_removing_file_from_directory_that_is_not_readable()
    {
        file_put_contents('file', 'data');
        chmod($this->workingDirectory, 0500);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not remove file "file"');
        $this->adapter->remove('file');
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function in_order_to_check_if_a_file_exists_given_filepath_must_be_a_non_empty_string($filePath)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected non-empty string for "filePath"');
        $this->adapter->exists($filePath);
    }

    /** @test */
    public function file_can_be_tested_to_exist()
    {
        file_put_contents("$this->workingDirectory/existing", 'boo');
        $this->assertTrue($this->adapter->exists('existing'));
        $this->assertFalse($this->adapter->exists('non-existent'));
    }

    /** @test */
    public function writes_files()
    {
        $this->adapter->writeTo('file', 'data');

        $this->assertFileExists('file');
    }

    /** @test */
    public function keeps_backups_for_overwritten_file()
    {
        $this->adapter->writeTo('file', 'data');
        $this->adapter->writeWithBackupTo('file', 'data');

        $this->assertFileExists('file');
        $this->assertFileExists('file.qatools-bak');
    }

    /** @test */
    public function keeps_no_backups_for_newly_written_file()
    {
        $this->adapter->writeWithBackupTo('file', 'data');

        $this->assertFileExists('file');
        $this->assertFileNotExists('file.qatools-bak');
    }

    /** @test */
    public function restores_backup_for_overwritten_file()
    {
        $this->adapter->writeTo('file', 'data');
        $this->adapter->writeWithBackupTo('file', 'newdata');
        $this->adapter->restoreBackupOf('file');

        $this->assertFileExists('file');
        $this->assertFileNotExists('file.qatools-bak');
        $this->assertSame('data', file_get_contents('file'));
    }

    /** @test */
    public function removes_newly_written_file_when_restoring_nonexistent_backup()
    {
        $this->adapter->writeWithBackupTo('file', 'newdata');
        $this->adapter->restoreBackupOf('file');

        $this->assertFileNotExists('file');
        $this->assertFileNotExists('file.qatools-bak');
    }

    /** @test */
    public function removes_backups()
    {
        $this->adapter->writeTo('file', 'data');
        $this->adapter->writeWithBackupTo('file', 'newdata');
        $this->adapter->discardBackupOf('file');

        $this->assertFileExists('file');
        $this->assertFileNotExists('file.qatools-bak');
        $this->assertSame('newdata', file_get_contents('file'));
    }
}
