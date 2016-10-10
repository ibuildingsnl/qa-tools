<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class WriteFileTask implements Task
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $fileContents;

    /**
     * @var int
     */
    private $mode;

    /**
     * @param string $filePath
     * @param string $fileContents
     * @param int    $mode
     */
    public function __construct($filePath, $fileContents, $mode = 0644)
    {
        Assertion::string($filePath, 'File path ought to be a string, got "%s" of type "%s"');
        Assertion::string($fileContents, 'File contents ought to be a string, got "%s" of type "%s"');

        $this->filePath = $filePath;
        $this->fileContents = $fileContents;
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getFileContents()
    {
        return $this->fileContents;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    public function __toString()
    {
        return sprintf(
            'WriteFileTask(filePath="%s", fileContents="%s, mode="%o")',
            $this->filePath,
            substr($this->fileContents, 0, 20),
            $this->mode
        );
    }
}
