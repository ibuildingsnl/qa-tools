<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;
use Mockery;
use PHPUnit\Framework\TestCase;

final class TaskHelperSetTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function a_template_path_that_can_be_set_can_only_be_a_string($path)
    {
        $this->expectException(InvalidArgumentException::class);

        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);

        $taskRegistry = new TaskHelperSet($dummyTemplateEngine);
        $taskRegistry->setTemplatePath($path);
    }

    /**
     * @test
     */
    public function a_template_path_can_be_set()
    {
        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);

        $path = 'some/file/path';

        $dummyTemplateEngine
            ->shouldReceive('setPath')
            ->with($path);

        $taskRegistry = new TaskHelperSet($dummyTemplateEngine);
        $taskRegistry->setTemplatePath($path);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function a_template_that_can_be_rendered_can_only_be_a_string($template)
    {
        $this->expectException(InvalidArgumentException::class);

        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);

        $taskRegistry = new TaskHelperSet($dummyTemplateEngine);
        $taskRegistry->renderTemplate($template);
    }

    /**
     * @test
     */
    public function a_template_is_rendered()
    {
        $dummyTemplateEngine = Mockery::mock(TemplateEngine::class);

        $template = 'some/file/path';

        $dummyTemplateEngine
            ->shouldReceive('render')
            ->with($template, []);

        $taskRegistry = new TaskHelperSet($dummyTemplateEngine);
        $taskRegistry->renderTemplate($template);
    }
}
