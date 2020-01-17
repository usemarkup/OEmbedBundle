<?php

declare(strict_types=1);

namespace Markup\OEmbedBundle\Service;

use Markup\OEmbedBundle\Cache\ObjectCache;
use Markup\OEmbedBundle\Client\ClientInterface;
use Markup\OEmbedBundle\Exception\InvalidOEmbedContentException;
use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
* A service for fetching oEmbed stuff from an implementing provider.
*/
class OEmbedService
{
    /**
     * A client for communicating with the oEmbed provider at a high level.
     *
     * @var ClientInterface
     **/
    private $client;

    /**
     * @var ContainerInterface
     */
    private $providerLocator;

    /**
     * @var ObjectCache
     **/
    private $objectCache;

    /**
     * @var string
     **/
    private $cacheKeyDelimiter;

    public function __construct(
        ClientInterface $client,
        ContainerInterface $providerLocator,
        ObjectCache $objectCache,
        string $cacheKeyDelimiter = ':'
    ) {
        $this->client = $client;
        $this->providerLocator = $providerLocator;
        $this->objectCache = $objectCache;
        $this->cacheKeyDelimiter = $cacheKeyDelimiter;
    }

    /**
     * Fetches an oEmbed object for a provider and a media ID.
     **/
    public function fetchOEmbed(string $provider, string $mediaId, array $parameters = array()): OEmbedInterface
    {
        if ($oEmbed = $this->objectCache->get($this->getCacheKey($provider, $mediaId))) {
            //TODO: deal with possibly different parameters
            return $oEmbed;
        }

        try {
            /** @var ProviderInterface $providerObject */
            $providerObject = $this->providerLocator->get($provider);
        } catch (NotFoundExceptionInterface $e) {
            throw new OEmbedUnavailableException($e->getMessage(), 0, $e);
        }

        try {
            $oEmbed = $this->client->fetchEmbed($providerObject, $mediaId, $parameters);
        } catch (InvalidOEmbedContentException $e) {
            throw new OEmbedUnavailableException($e->getMessage(), 0, $e);
        }

        $this->objectCache->set($this->getCacheKey($provider, $mediaId), $oEmbed);

        return $oEmbed;
    }

    /**
     * Gets a cache key formed for the provided provider and media ID.
     *
     * @param  string $provider
     * @param  string $mediaId
     * @return string
     **/
    private function getCacheKey($provider, $mediaId)
    {
        return $provider . $this->cacheKeyDelimiter . $mediaId;
    }
}
