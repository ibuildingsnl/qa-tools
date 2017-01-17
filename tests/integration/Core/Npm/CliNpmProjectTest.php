<?php

namespace integration\Core\Npm;

use Ibuildings\QaTools\Core\Cli\Cli;
use Ibuildings\QaTools\Core\Npm\CliNpmProject;
use Ibuildings\QaTools\Core\Project\Directory;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

final class CliNpmProjectTest extends TestCase
{
    /** @var LoggerInterface|Mock */
    private $logger;
    /** @var CliNpmProject */
    private $project;

    public function setUp()
    {
        $tempDirectory = sys_get_temp_dir().'/qa-npm';
        $filesystem = new Filesystem();
        if ($filesystem->exists($tempDirectory)) {
            $filesystem->remove($tempDirectory);
        }
        $filesystem->mkdir($tempDirectory);

        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->project = new CliNpmProject(new Directory($tempDirectory), 'npm', $this->logger);
    }

    /**
     * @test
     */
    public function fails_on_dependency_installation_problem()
    {
        if (!Cli::isExecutableInstalled('npm')) {
            $this->markTestSkipped('Unable to run integration test against NPM because NPM is not installed');
        }

        $this->assertEquals(
            false,
            $this->project->verifyDevDependenciesCanBeInstalled(['some-non-existing-package@99.99.99'])
        );
    }

    /**
     * @test
     */
    public function succeeds_when_packages_could_be_installed()
    {
        if (!Cli::isExecutableInstalled('npm')) {
            $this->markTestSkipped('Unable to run integration test against NPM because NPM is not installed');
        }

        $this->assertEquals(
            true,
            $this->project->verifyDevDependenciesCanBeInstalled(['eslint@3.10.0'])
        );
    }
}
