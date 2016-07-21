<?php

namespace Ibuildings\QaTools\UnitTest\Core\IO\File;

use Exception;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\IO\File\FilesystemAdapter;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group IO
 * @group Filesystem
 */
class FilesystemAdapterTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notString
     */
    public function data_to_write_to_file_must_be_a_string($nonString)
    {
        $filesystemAdapter = new FilesystemAdapter(Mockery::mock(Filesystem::class));

        $this->expectException(InvalidArgumentException::class);
        $filesystemAdapter->writeTo($nonString, '/some/path');
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function in_order_to_write_given_filepath_must_be_a_string($nonStringOrEmptyString)
    {
        $filesystemAdapter = new FilesystemAdapter(Mockery::mock(Filesystem::class));

        $this->expectException(InvalidArgumentException::class);
        $filesystemAdapter->writeTo('data-to-write', $nonStringOrEmptyString);
    }

    /**
     * @test
     */
    public function an_exception_thrown_by_filesystem_when_writing_is_converted_to_an_qa_tools_exception()
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock->shouldReceive('dumpFile')->andThrow(IOException::class);

        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

        try {
            $filesystemAdapter->writeTo('data-to-write', '/some/path');
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

        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

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

        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

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
        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

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

        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

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
        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

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

        $filesystemAdapter = new FilesystemAdapter($filesystemMock);
        $filesystemAdapter->exists($filePath);
    }
}
