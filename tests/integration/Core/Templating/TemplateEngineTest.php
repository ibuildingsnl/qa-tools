<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Templating;

use Exception;
use Ibuildings\QaTools\Core\Application\Application;
use Ibuildings\QaTools\Core\Application\ContainerLoader;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;
use Ibuildings\QaTools\IntegrationTest\ContainerTestCase;
use Mockery as m;
use Twig_Error_Runtime;

/**
 * @group Templating
 */
class TemplateEngineTestCase extends ContainerTestCase
{
    /** @test */
    public function twig_requires_variables_to_exist()
    {
        $twig = $this->container->get('twig.environment');

        $engine = new TemplateEngine($twig);
        $engine->setPath(__DIR__ . '/templates');

        $this->expectException(Twig_Error_Runtime::class);
        $this->expectExceptionMessage('Variable "variableDoesNotExist" does not exist in ');
        $engine->render('non-existent-variable.twig', []);
    }
}
