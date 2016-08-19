<?php

namespace Ibuildings\QaTools\UnitTest\Tool\PhpMd;

use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Configuration\RequirementHelperSet;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\AutomatedResponseInterviewer;
use Ibuildings\QaTools\Core\Requirement\Specification\ComposerDevDependenciesRequirementSpecification;
use Ibuildings\QaTools\Tool\PhpMd\Configurator\PhpMdSf2Configurator;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Ibuildings\QaTools\UnitTest\Requirements;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Tool
 * @group PhpMd
 */
class PhpMdSf2ConfiguratorTest extends TestCase
{
    /** @var AutomatedResponseInterviewer */
    private $interviewer;
    /** @var RequirementDirectory|MockInterface */
    private $requirementDirectory;
    /** @var RequirementHelperSet|MockInterface */
    private $requirementHelperSet;

    protected function setUp()
    {
        $this->interviewer = new AutomatedResponseInterviewer();
        $this->requirementDirectory = Mockery::spy(RequirementDirectory::class);
        $this->requirementHelperSet = Mockery::mock(RequirementHelperSet::class);
    }

    /** @test */
    public function installs_phpmd_when_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Mess Detector?', YesOrNoAnswer::yes());

        $configurator = new PhpMdSf2Configurator();
        $configurator->configure($this->interviewer, $this->requirementDirectory, $this->requirementHelperSet);

        $this->requirementDirectory
            ->shouldHaveReceived('registerRequirement')
            ->with(
                Requirements::requirementMatching(
                    ComposerDevDependenciesRequirementSpecification::ofAnyVersion(new PackageName('phpmd/phpmd'))
                ),
                PhpMd::class
            );
    }

    /** @test */
    public function does_not_install_phpmd_when_not_desired()
    {
        $this->interviewer->recordAnswer('Would you like to use PHP Mess Detector?', YesOrNoAnswer::no());

        $configurator = new PhpMdSf2Configurator();
        $configurator->configure($this->interviewer, $this->requirementDirectory, $this->requirementHelperSet);

        $this->requirementDirectory->shouldNotHaveReceived('registerRequirement');
    }
}
