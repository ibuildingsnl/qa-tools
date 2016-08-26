<?php

namespace Ibuildings\QaTools\Core\Application\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterTaskExecutorsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $executorExecutorDefinition = $container->findDefinition('qa_tools.task.task_directory_executor');
        $taggedExecutorIds = array_unique(array_keys($container->findTaggedServiceIds('qa_tools.task_executor')));
        $taggedExecutorReferences = array_map(
            function ($serviceId) {
                return new Reference($serviceId);
            },
            $taggedExecutorIds
        );

        $executorExecutorDefinition->replaceArgument(0, $taggedExecutorReferences);
    }
}
