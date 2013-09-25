<?php

namespace Markup\OEmbedBundle\Client;

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
     **/
    public function fetchEmbed(ProviderInterface $provider, $mediaId, array $parameters = array());
}
