<?php

namespace Ibuildings\QaTools\UnitTest\Core\Application\Compiler;

use Ibuildings\QaTools\Core\Application\Compiler\RegisterTaskExecutorsCompilerPass;
use Mockery;
use PHPUnit\Framework\TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group Application
 * @group CompilerPass
 */
class RegisterTaskExecutorsCompilerPassTest extends TestCase
{
    /** @test */
    public function registers_task_executors()
    {
        $executorExecutorDefinition = Mockery::spy(Definition::class);

        $container = Mockery::mock(ContainerBuilder::class);
        $container
            ->shouldReceive('findDefinition')
            ->with('qa_tools.task.task_directory_executor')
            ->andReturn($executorExecutorDefinition);
        $container
            ->shouldReceive('findTaggedServiceIds')
            ->with('qa_tools.task_executor')
            ->andReturn(['service_a' => [], 'service_b' => []]);

        $compilerPass = new RegisterTaskExecutorsCompilerPass();
        $compilerPass->process($container);

        $executorExecutorDefinition
            ->shouldHaveReceived('replaceArgument')
            ->with(0, Mockery::anyOf([new Reference('service_a'), new Reference('service_b')]))
            ->once();
    }
}
