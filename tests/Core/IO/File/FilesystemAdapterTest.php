<?php

use Ibuildings\QaTools\Core\IO\File\FilesystemAdapter;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemAdapterTest extends TestCase
{
    /**
     * @test
     *
     * @group IO
     * @group Filesystem
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString
     *
     * @param mixed $nonString
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
     * @group IO
     * @group Filesystem
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notStringOrEmptyString
     *
     * @param mixed $nonStringOrEmptyString
     */
    public function in_order_to_write_given_filepath_must_be_a_string($nonStringOrEmptyString)
    {
        $filesystemAdapter = new FilesystemAdapter(Mockery::mock(Filesystem::class));

        $this->expectException(InvalidArgumentException::class);
        $filesystemAdapter->writeTo('data-to-write', $nonStringOrEmptyString);
    }

    /**
     * @test
     *
     * @group IO
     * @group Filesystem
     */
    public function an_exception_thrown_by_filesystem_when_writing_is_converted_to_an_engineblock_exception()
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock->shouldReceive('dumpFile')->andThrow(IOException::class);

        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

        try {
            $filesystemAdapter->writeTo('data-to-write', '/some/path');
        } catch (Exception $exception) {
            $this->assertInstanceOf('\Ibuildings\QaTools\Exception\RuntimeException', $exception);
            $this->assertInstanceOf(IOException::class, $exception->getPrevious());
        }
    }

    /**
     * @test
     *
     * @group IO
     * @group Filesystem
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
     *
     * @group IO
     * @group Filesystem
     */
    public function attempting_to_read_data_from_a_file_that_is_not_readable_fails()
    {
        $filesystemMock = Mockery::mock(Filesystem::class);
        $filesystemMock->shouldReceive('exists')->andReturn(true);

        $filesystemAdapter = new FilesystemAdapter($filesystemMock);

        $this->expectException(RuntimeException::class);
        $filesystemAdapter->readFrom('/this/is/not/readable');
    }
}
