<?php

namespace Ibuildings\QaTools\IntegrationTest;

use Ibuildings\QaTools\Core\Application\Application;
use Ibuildings\QaTools\Core\Application\CompiledContainer;
use Ibuildings\QaTools\Core\Application\ContainerLoader;
use Ibuildings\QaTools\Test\MockeryTestCase;

abstract class ContainerTestCase extends MockeryTestCase
{
    /** @var CompiledContainer */
    protected $container;

    protected function setUp()
    {
        $application = new Application(true);
        $container = ContainerLoader::load($application, true);

        $this->container = $container;
    }
}
