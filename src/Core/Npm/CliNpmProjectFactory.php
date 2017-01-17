<?php

namespace Ibuildings\QaTools\Core\Npm;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Project\Directory;

class CliNpmProjectFactory implements NpmProjectFactory
{
    /**
     * @param string $directory
     * @return CliNpmProject
     * @throws \Assert\AssertionFailedException
     */
    public function forDirectory($directory)
    {
        Assertion::string($directory, 'NPM project directory ought to be a string, got "%s" of type "%s"');

        return new CliNpmProject(new Directory($directory), 'npm');
    }
}
