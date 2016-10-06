<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Psr\Log\LoggerInterface;

final class CliComposerProjectFactory implements ProjectFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function forDirectory($directory)
    {
        Assertion::string($directory, 'Composer project directory ought to be a string, got "%s" of type "%s"');

        $envComposerPath = getenv('COMPOSER_BIN');
        $pharPath = $directory . '/composer.phar';

        if ($envComposerPath) {
            $composerBinary = $envComposerPath;
        } elseif (file_exists($pharPath)) {
            $composerBinary = $pharPath;
        } else {
            $composerBinary = 'composer';
        }

        return new CliComposerProject($directory, $composerBinary, $this->logger);
    }
}
