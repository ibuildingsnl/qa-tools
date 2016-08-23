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
     * @param string $filePath
     * @param string $fileContents
     */
    public function __construct($filePath, $fileContents)
    {
        Assertion::string('File path ought to be a string, got "%s" of type "%s"');
        Assertion::string('File contents ought to be a string, got "%s" of type "%s"');

        $this->filePath = $filePath;
        $this->fileContents = $fileContents;
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

    public function __toString()
    {
        return sprintf(
            'WriteFileTask(filePath="%s", fileContents="%s")',
            $this->filePath,
            substr($this->fileContents, 0, 20)
        );
    }
}
