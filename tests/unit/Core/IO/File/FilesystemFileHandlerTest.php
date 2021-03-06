<?php

namespace Ibuildings\QaTools\UnitTest\Core\IO\File;

use Exception;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\IO\File\FilesystemFileHandler;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Mockery;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group IO
 * @group Filesystem
 */
class FilesystemFileHandlerTest extends MockeryTestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notString
     */
    public function data_to_write_to_file_must_be_a_string($nonString)
    {
        $filesystemAdapter = new FilesystemFileHandler(Mockery::mock(Filesystem::class));

        $this->expectException(InvalidArgumentException::class);
        $filesystemAdapter->writeTo('/some/path', $nonString);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function in_order_to_write_given_filepath_must_be_a_string($nonStringOrEmptyString)
    {
        $filesystemAdapter = new FilesystemFileHandler(Mockery::mock(Filesystem::class));

        $this->expectException(InvalidArgumentException::class);
        $filesystemAdapter->writeTo($nonStringOrEmptyString, 'data-to-write');
    }

    /**
     * @test
     */
    public function an_exception_thrown_by_filesystem_when_writing_is_converted_to_an_qa_tools_exception()
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock->shouldReceive('dumpFile')->andThrow(IOException::class);

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        try {
            $filesystemAdapter->writeTo('/some/path', 'data-to-write');
        } catch (Exception $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
            $this->assertInstanceOf(IOException::class, $exception->getPrevious());
        }
    }

    /**
     * @test
     */
    public function attempting_to_read_data_from_a_non_existent_file_fails()
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock->shouldReceive('exists')->andReturn(false);

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        $this->expectException(RuntimeException::class);
        $filesystemAdapter->readFrom('/does/not/exist');
    }

    /**
     * @test
     */
    public function attempting_to_read_data_from_a_file_that_is_not_readable_fails()
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock->shouldReceive('exists')->andReturn(true);

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        $this->expectException(RuntimeException::class);
        $filesystemAdapter->readFrom('/this/is/not/readable');
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function in_order_to_remove_given_filepath_must_be_a_non_empty_string($filePath)
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        $this->expectException(InvalidArgumentException::class);

        $filesystemAdapter->remove($filePath);
    }

    /**
     * @test
     */
    public function an_exception_thrown_by_filesystem_when_removing_is_converted_to_an_qa_tools_exception()
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock->shouldReceive('remove')->andThrow(IOException::class);

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        try {
            $filesystemAdapter->remove('/some/path');
        } catch (Exception $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
            $this->assertInstanceOf(IOException::class, $exception->getPrevious());
        }
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function in_order_to_check_if_a_file_exists_given_filepath_must_be_a_non_empty_string($filePath)
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        $this->expectException(InvalidArgumentException::class);

        $filesystemAdapter->exists($filePath);
    }

    /**
     * @test
     */
    public function filesystem_is_used_to_check_if_a_file_exists()
    {
        $filePath = 'some/file/path';

        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock
            ->shouldReceive('exists')
            ->with($filePath);

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);
        $filesystemAdapter->exists($filePath);
    }

    /** @test */
    public function wraps_ioexception_in_runtimeexception_when_writing_with_backup()
    {
        $filePath = 'some/file/path';

        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock
            ->shouldReceive('exists')
            ->with($filePath)
            ->andReturn(true);
        $filesystemMock
            ->shouldReceive('copy')
            ->with($filePath, "$filePath.qatools-bak")
            ->andThrow(new IOException('msg'));

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        try {
            $filesystemAdapter->writeWithBackupTo($filePath, 'data');
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertInstanceOf(IOException::class, $e->getPrevious());
        }
    }

    /**
     * @test
     */
    public function filesystem_is_used_to_change_mode_of_file()
    {
        $filePath = 'some/file/path';
        $mode = 0755;

        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock
            ->shouldReceive('chmod')
            ->with($filePath, $mode);

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);
        $filesystemAdapter->changeMode($filePath, $mode);
    }

    /** @test */
    public function wraps_ioexception_in_runtimeexception_when_changing_mode()
    {
        $filePath = 'some/file/path';
        $mode = 0755;

        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock
            ->shouldReceive('chmod')
            ->with($filePath, $mode)
            ->andThrow(new IOException('msg'));

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        try {
            $filesystemAdapter->changeMode($filePath, $mode);
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertInstanceOf(IOException::class, $e->getPrevious());
        }
    }

    /** @test */
    public function wraps_ioexception_in_runtimeexception_when_restoring_backup_of_newly_written_file()
    {
        $filePath = 'some/file/path';

        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock
            ->shouldReceive('exists')
            ->with("$filePath.qatools-bak")
            ->andReturn(false);
        $filesystemMock
            ->shouldReceive('remove')
            ->with($filePath)
            ->andThrow(new IOException('msg'));

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        try {
            $filesystemAdapter->restoreBackupOf($filePath);
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertInstanceOf(IOException::class, $e->getPrevious());
        }
    }

    /** @test */
    public function wraps_ioexception_in_runtimeexception_when_restoring_backup_of_overwritten_file()
    {
        $filePath = 'some/file/path';

        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock
            ->shouldReceive('exists')
            ->with("$filePath.qatools-bak")
            ->andReturn(true);
        $filesystemMock
            ->shouldReceive('rename')
            ->with("$filePath.qatools-bak", $filePath, true)
            ->andThrow(new IOException('msg'));

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        try {
            $filesystemAdapter->restoreBackupOf($filePath);
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertInstanceOf(IOException::class, $e->getPrevious());
        }
    }

    /** @test */
    public function wraps_ioexception_in_runtimeexception_when_discarding_backup()
    {
        $filePath = 'some/file/path';

        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock
            ->shouldReceive('remove')
            ->with("$filePath.qatools-bak")
            ->andThrow(new IOException('msg'));

        $filesystemAdapter = new FilesystemFileHandler($filesystemMock);

        try {
            $filesystemAdapter->discardBackupOf($filePath);
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertInstanceOf(IOException::class, $e->getPrevious());
        }
    }
}
