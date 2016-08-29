<?php

namespace Ibuildings\QaTools\Core\Application\Compiler;

use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterTaskExecutorsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $executorExecutorDefinition = $container->findDefinition('qa_tools.task.task_directory_executor');
        $taggedExecutors = $container->findTaggedServiceIds('qa_tools.task_executor');

        $prioritisedReferences = [];
        foreach ($taggedExecutors as $serviceId => $tag) {
            if (count($tag) !== 1) {
                throw new InvalidConfigurationException(
                    sprintf(
                        'The tag "qa_tools.task_executor" is specified twice on service "%s"; ' .
                        'a service may only be registered once as being a task executor',
                        $serviceId
                    )
                );
            }

            if (!isset($tag[0]['priority'])) {
                throw new InvalidConfigurationException(
                    sprintf(
                        'Tag "qa_tools.task_executor" for service "%s" ought to have a property "priority"',
                        $serviceId
                    )
                );
            }

            $priority = $tag[0]['priority'];
            if (!is_int($priority)) {
                throw new InvalidConfigurationException(
                    sprintf(
                        'Task executor "%s" priority is not an integer, but is of type "%s"',
                        $serviceId,
                        gettype($priority)
                    )
                );
            }
            if (isset($prioritisedReferences[$priority])) {
                throw new InvalidConfigurationException(
                    sprintf(
                        'Cannot register task executor "%s" with priority "%d"; ' .
                        'task executor "%s" is already registered at that priority',
                        $serviceId,
                        $priority,
                        $prioritisedReferences[$priority]
                    )
                );
            }

            $prioritisedReferences[$priority] = new Reference($serviceId);
        }

        if (!ksort($prioritisedReferences)) {
            throw new RuntimeException('Could not sort task executors based on prioritisation (ksort failed)');
        }
        $prioritisedReferences = array_reverse($prioritisedReferences);

        $executorExecutorDefinition->replaceArgument(0, $prioritisedReferences);
    }
}
