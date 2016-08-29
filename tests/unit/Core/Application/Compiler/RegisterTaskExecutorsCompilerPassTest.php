<?php

namespace Ibuildings\QaTools\UnitTest\Core\Application\Compiler;

use Ibuildings\QaTools\Core\Application\Compiler\RegisterTaskExecutorsCompilerPass;
use Mockery;
use PHPUnit\Framework\TestCase as TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group Application
 * @group CompilerPass
 */
class RegisterTaskExecutorsCompilerPassTest extends TestCase
{
    /** @var Definition */
    private $executorExecutorDefinition;
    /** @var ContainerBuilder */
    private $container;
    /** @var RegisterTaskExecutorsCompilerPass */
    private $compilerPass;

    protected function setUp()
    {
        $this->executorExecutorDefinition = Mockery::spy(Definition::class);

        $this->container = Mockery::mock(ContainerBuilder::class);
        $this->container
            ->shouldReceive('findDefinition')
            ->with('qa_tools.task.task_directory_executor')
            ->andReturn($this->executorExecutorDefinition);

        $this->compilerPass = new RegisterTaskExecutorsCompilerPass();
    }

    /** @test */
    public function registers_task_executors_in_the_order_of_their_priority_when_defined_inorder()
    {
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('qa_tools.task_executor')
            ->andReturn(['service_a' => array(['priority' => 10]), 'service_b' => array(['priority' => 0])]);

        $this->compilerPass->process($this->container);

        $this->executorExecutorDefinition
            ->shouldHaveReceived('replaceArgument')
            ->with(0, Mockery::anyOf([new Reference('service_a'), new Reference('service_b')]))
            ->once();
    }

    /** @test */
    public function registers_task_executors_in_the_order_of_their_priority_when_defined_outoforder()
    {
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('qa_tools.task_executor')
            ->andReturn(['service_a' => array(['priority' => 0]), 'service_b' => array(['priority' => 10])]);

        $this->compilerPass->process($this->container);

        $this->executorExecutorDefinition
            ->shouldHaveReceived('replaceArgument')
            ->with(0, Mockery::anyOf([new Reference('service_b'), new Reference('service_a')]))
            ->once();
    }

    /** @test */
    public function doesnt_allow_two_tags_per_service()
    {
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('qa_tools.task_executor')
            ->andReturn(['service_a' => array(['priority' => 0], ['priority' => 10])]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The tag "qa_tools.task_executor" is specified twice on service "service_a"');
        $this->compilerPass->process($this->container);
    }

    /** @test */
    public function doesnt_allow_two_task_executors_to_be_registered_with_the_same_priority()
    {
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('qa_tools.task_executor')
            ->andReturn(['service_a' => array(['priority' => 10]), 'service_b' => array(['priority' => 10])]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('task executor "service_a" is already registered at that priority');
        $this->compilerPass->process($this->container);
    }
}
