<?php

namespace Ibuildings\QaTools\Core\Application\Compiler;

use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterConfiguratorsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $runListDefinition   = $container->findDefinition('qa_tools.run_list');
        $taggedConfigurators = $container->findTaggedServiceIds('qa_tools.tool');

        foreach ($taggedConfigurators as $configurator => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['project_type'])) {
                    throw new LogicException(sprintf(
                        'Cannot register Configurator "%s" for a ProjectType: property "project_type" not found on tag',
                        $configurator
                    ));
                }

                $projectType = new ProjectType($tag['project_type']);

                $projectTypeDefinition = new Definition(ProjectType::class, [$projectType->getProjectType()]);
                $container->setDefinition(
                    'qa_tools.project.project_type.' . $projectType->getProjectType(),
                    $projectTypeDefinition
                );

                $runListDefinition->addMethodCall(
                    'registerFor',
                    [
                        new Reference($configurator),
                        $projectTypeDefinition,
                    ]
                );
            }
        }
    }
}
