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
        Assertion::nonEmptyString($filePath, 'File path ought to be a non-empty string, got "%s" of type "%s"');

        try {
            $this->filesystem->dumpFile($filePath, $data);
        } catch (IOException $exception) {
            $newMessage = sprintf('Could not write data to file "%s": "%s"', $filePath, $exception->getMessage());
            throw new RuntimeException($newMessage, null, $exception);
        }
    }

    public function readFrom($filePath)
    {
        Assertion::nonEmptyString($filePath, 'Expected non-empty string for "%3$s", "%1$s" given', 'filePath');

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
        Assertion::nonEmptyString($filePath, 'Expected non-empty string for "%3$s", "%1$s" given', 'filePath');

        try {
            $this->filesystem->remove($filePath);
        } catch (IOException $exception) {
            $newMessage = sprintf('Could not remove file "%s": "%s"', $filePath, $exception->getMessage());
            throw new RuntimeException($newMessage, null, $exception);
        }
    }

    public function exists($filePath)
    {
        Assertion::nonEmptyString($filePath, 'Expected non-empty string for "%3$s", "%1$s" given', 'filePath');

        return $this->filesystem->exists($filePath);
    }
}
