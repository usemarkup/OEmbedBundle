<?php

namespace Markup\OEmbedBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('markup_o_embed');

        $rootNode
            ->fixXmlConfig('provider')
            ->children()
                ->arrayNode('providers')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('endpoint')
                                ->isRequired()
                            ->end()
                            ->scalarNode('scheme')
                                ->isRequired()
                            ->end()
                            ->scalarNode('code_property')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('squash_rendering_errors')
                    ->defaultTrue()
                ->end()
                ->arrayNode('cache')
                    ->children()
                        ->scalarNode('id')
                        ->end()
                        ->scalarNode('key_delimiter')
                            ->defaultValue(':')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
