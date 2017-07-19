<?php

namespace Markup\OEmbedBundle\Tests\Client;

use Markup\OEmbedBundle\Client\AbstractClient;
use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Provider\ProviderInterface;

class MockClient extends AbstractClient
{
    /**
     * Fetches an oEmbed instance from the provider.
     *
     * @param  ProviderInterface $provider
     * @param  string            $mediaId
     * @param  array             $parameters
     * @return OEmbedInterface
     **/
    public function fetchEmbed(ProviderInterface $provider, string $mediaId, array $parameters = []): OEmbedInterface
    {
        // empty implementation
    }
}
