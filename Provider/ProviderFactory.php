<?php
declare(strict_types=1);

namespace Markup\OEmbedBundle\Provider;

use Markup\OEmbedBundle\Exception\ProviderNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
* A factory for providers, using the container.
*/
class ProviderFactory
{
    /**
     * @var ContainerInterface
     **/
    private $container;

    /**
     * @var string
     **/
    private $servicePrefix;

    public function __construct(ContainerInterface $container, string $servicePrefix)
    {
        $this->container = $container;
        $this->servicePrefix = $servicePrefix;
    }

    /**
     * Fetches the provider with the given name. If not found, throws a provider not found exception.
     *
     * @param  string                    $name
     * @return ProviderInterface
     * @throws ProviderNotFoundException if provider not found.
     **/
    public function fetchProvider(string $name): ProviderInterface
    {
        try {
            $provider = $this->container->get($this->servicePrefix . '.' . $name);
        } catch (ServiceNotFoundException $e) {
            throw new ProviderNotFoundException($name);
        }
        if (!$provider instanceof ProviderInterface) {
            throw new ProviderNotFoundException($name);
        }

        return $provider;
    }
}
