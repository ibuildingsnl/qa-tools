<?php

namespace Ibuildings\QaTools\Core\IO\File;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class FilesystemAdapter implements FileHandler
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function writeTo($data, $filePath)
    {
        Assertion::string($data, 'Can only write string data to file, "%s" given');
        Assertion::nonEmptyString($filePath, 'filePath');

        try {
            $this->filesystem->dumpFile($filePath, $data);
        } catch (IOException $exception) {
            $newMessage = sprintf('Could not write data to file "%s": "%s"', $filePath, $exception->getMessage());
            throw new RuntimeException($newMessage, null, $exception);
        }
    }

    public function readFrom($filePath)
    {
        Assertion::nonEmptyString($filePath, 'filePath');

        if (!$this->filesystem->exists($filePath)) {
            throw new RuntimeException(sprintf('Cannot read from file "%s" as it does not exist', $filePath));
        }

        if (!is_readable($filePath)) {
            throw new RuntimeException(sprintf('Cannot read from file "%s" as it is not readable', $filePath));
        }

        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new RuntimeException(sprintf('Could not read data from file "%s"', $filePath));
        }

        return $data;
    }

    public function remove($filePath)
    {
        try {
            $this->filesystem->remove($filePath);
        } catch (IOException $exception) {
            $newMessage = sprintf('Could not remove file "%s", "%s"', $filePath, $exception->getMessage());
            throw new RuntimeException($newMessage, null, $exception);
        }
    }
}
