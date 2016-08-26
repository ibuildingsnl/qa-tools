<?php

namespace Ibuildings\QaTools\Core\Application\Compiler;

use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterTaskExecutorsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $executorExecutorDefinition = $container->findDefinition('qa_tools.task.task_directory_executor');

        $taggedExecutors = $container->findTaggedServiceIds('qa_tools.task_executor');
        $executorPriorityTuples = array_map(
            function ($serviceId, $tags) {
                if (count($tags) > 1) {
                    throw new RuntimeException(
                        sprintf(
                            'Task executor "%s" may not be registered more than once (%d times), ' .
                            'please check its service definition\'s tags',
                            $serviceId,
                            count($tags)
                        )
                    );
                }
                if (!isset($tags[0]['priority'])) {
                    throw new RuntimeException(
                        sprintf(
                            'Tag "qa_tools.task_executor" for service "%s" ought to have a property "priority"',
                            $serviceId
                        )
                    );
                }
                $priorityIsInteger = is_int($tags[0]['priority']);
                $priorityConsistsOfDigits = is_string($tags[0]['priority']) && ctype_digit($tags[0]['priority']);
                if (!$priorityIsInteger || $priorityConsistsOfDigits) {
                    throw new RuntimeException(
                        sprintf(
                            'Tag "qa_tools.task_executor" property "priority" of service "%s" ought to be an integer',
                            $serviceId
                        )
                    );
                }

                return [$serviceId, (int) $tags[0]['priority']];
            },
            array_keys($taggedExecutors),
            $taggedExecutors
        );

        $sortedExecutorTuples = $executorPriorityTuples;
        usort(
            $sortedExecutorTuples,
            function (array $tupleA, array $tupleB) {
                return $tupleB[1] - $tupleA[1];
            }
        );
        $taggedExecutorReferences = array_map(
            function (array $tuple) {
                return new Reference($tuple[0]);
            },
            $sortedExecutorTuples
        );

        $executorExecutorDefinition->replaceArgument(0, $taggedExecutorReferences);
    }
}
