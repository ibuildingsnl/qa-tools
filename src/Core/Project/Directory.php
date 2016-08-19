<?php

namespace Ibuildings\QaTools\Core\Project;

use Assert\Assertion;

final class Directory
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        Assertion::string($directory, 'Project directory ought to be a string, got "%s" of type "%s"');

        $directory = str_replace('\\', '/', $directory);
        $directory = rtrim($directory, '/') . '/';
        $directory = preg_replace('~^\\./|/.$~', '', $directory);
        $directory = str_replace('/./', '/', $directory);

        if ($directory === '') {
            $directory = './';
        }

        $this->directory = $directory;
    }

    /**
     * @param Directory $directory
     * @return bool
     */
    public function equals(Directory $directory)
    {
        return $this->directory === $directory->directory;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $this->directory);
    }

    public function __toString()
    {
        return sprintf('Directory("%s")', $this->directory);
    }
}
