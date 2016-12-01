<?php

namespace A5sys\QowisioCloudApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('qowisio_cloud_api');

        $rootNode
            ->children()
                ->arrayNode('authentication')
                    ->children()
                        ->scalarNode('email')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('endpoints')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('authentication')->defaultValue('https://auth.qowisio.com/')->end()
                        ->scalarNode('data')->defaultValue('https://api.qowisio.com/')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
