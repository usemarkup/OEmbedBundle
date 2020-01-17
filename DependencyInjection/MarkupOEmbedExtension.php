<?php

namespace Markup\OEmbedBundle\DependencyInjection;

use Markup\OEmbedBundle\Cache\NullCache;
use Markup\OEmbedBundle\Provider\SimpleProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MarkupOEmbedExtension extends Extension
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->loadSquashRenderingErrors($config, $container);

        $loader->load('services.yml');

        $this->loadCacheServices($config, $container);
        $this->loadProviders($config, $container);
    }

    /**
     * Loads in the defined providers.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     * @return void
     **/
    private function loadProviders(array $config, ContainerBuilder $container)
    {
        if (!isset($config['providers'])) {
            return;
        }
        $providerLocator = $container->findDefinition('markup_oembed.provider_locator');
        foreach ($config['providers'] as $providerName => $provider) {
            $definition = (new Definition(
                SimpleProvider::class,
                [
                    $providerName,
                    $provider['endpoint'],
                    $provider['scheme'],
                    $provider['code_property'],
                ]
            ))->setPublic(false);

            $providerId = sprintf('markup_oembed.provider.%s', $providerName);
            $container->setDefinition($providerId, $definition);
            $providerLocator->setArguments([
                array_merge(
                    $providerLocator->getArguments()[0],
                    [
                        $providerName => new Reference($providerId),
                    ]
                )
            ]);
        }
    }

    /**
     * Loads whether rendering errors should be squashed.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     * @return void
     **/
    private function loadSquashRenderingErrors(array $config, ContainerBuilder $container)
    {
        $container->setParameter('markup_oembed.squash_rendering_errors', $config['squash_rendering_errors']);
    }

    /**
     * Loads cache-related services.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     * @return void
     **/
    private function loadCacheServices(array $config, ContainerBuilder $container)
    {
        $cacheServiceId = 'markup_oembed.string_cache';
        if (!isset($config['cache']) || !isset($config['cache']['id'])) {
            $container->setDefinition($cacheServiceId, new Definition(NullCache::class));
        } else {
            $container->setAlias($cacheServiceId, $config['cache']['id']);
        }
        if (isset($config['cache'])) {
            $container->setParameter('markup_oembed.cache_key_delimiter', $config['cache']['key_delimiter']);
        } else {
            $container->setParameter('markup_oembed.cache_key_delimiter', $container->getParameter('markup_oembed.cache_key_delimiter.default'));
        }
    }
}
