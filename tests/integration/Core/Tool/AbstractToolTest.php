<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Tool;

use Ibuildings\QaTools\Core\Application\Basedir;
use Ibuildings\QaTools\IntegrationTest\Core\Tool\Hammer\Hammer;
use PHPUnit\Framework\TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @group Tool
 */
class AbstractToolTest extends TestCase
{
    /** @test */
    public function resource_path_is_a_relative_path()
    {
        $containerBuilder = new ContainerBuilder();

        $tool = new Hammer();
        $tool->build($containerBuilder);

        $resourcePath = $containerBuilder->getParameter('tool.' . Hammer::class . '.resource_path');
        $this->assertStringStartsNotWith(
            '/',
            $resourcePath,
            'Resource path is an Unix-style absolute path. It must be a relative path.'
        );
        $this->assertLessThanOrEqual(
            1,
            substr_count($resourcePath, '..'),
            'Resource path should not traverse up out of the QA Tools project directory structure'
        );
        $this->assertFileExists(__DIR__ . '/../../../../bin/' . $resourcePath);
    }
}
