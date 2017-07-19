<?php

namespace Markup\OEmbedBundle\Client;

use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderInterface;

/**
 * An interface for the transport layer (at a high level).
 **/
interface ClientInterface
{
    /**
     * Fetches an oEmbed instance from the provider.
     *
     * @param  ProviderInterface $provider
     * @param  string            $mediaId
     * @param  array             $parameters
     * @return OEmbedInterface
     * @throws OEmbedUnavailableException if oEmbed cannot be resolved
     **/
    public function fetchEmbed(ProviderInterface $provider, string $mediaId, array $parameters = []): OEmbedInterface;
}
