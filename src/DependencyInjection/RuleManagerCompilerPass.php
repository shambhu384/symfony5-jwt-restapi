<?php


declare(strict_types=1);


namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RuleManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('app.rule_manager')) {
            return;
        }

        $definition = $container->findDefinition(
            'app.rule_manager'
        );
        $taggedServices = $container->findTaggedServiceIds(
            'rule_manager.rule'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addRule',
                array(new Reference($id))
            );
        }
    }
}
