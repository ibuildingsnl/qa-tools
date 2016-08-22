<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class CliComposerProjectFactory implements ProjectFactory
{
    public function forDirectory($directory)
    {
        Assertion::string($directory, 'Composer project directory ought to be a string, got "%s" of type "%s"');

        $composerBinary = getenv('COMPOSER_BIN') ?: 'composer';

        return new CliComposerProject($directory, $composerBinary);
    }
}
