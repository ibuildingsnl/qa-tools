<?php

namespace Ibuildings\QaTools\Core\Application\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CommandsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('qa_tools.application')) {
            return;
        }

        $definition = $container->findDefinition('qa_tools.application');
        $taggedServices = $container->findTaggedServiceIds('qa_tools.command');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
