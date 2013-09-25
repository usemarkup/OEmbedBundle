<?php

namespace Markup\OEmbedBundle\Client;

use Markup\OEmbedBundle\Provider\ProviderInterface;

/**
* A superclass for client implementations.
*/
abstract class AbstractClient implements ClientInterface
{
    /**
     * Resolves the media ID and an oEmbed provider to a URL.
     *
     * @param  ProviderInterface $provider
     * @param  string            $mediaId
     * @param  array             $parameters
     * @return string
     **/
    protected function resolveOEmbedUrl(ProviderInterface $provider, $mediaId, array $parameters = array())
    {
        $mediaUrl = str_replace('$ID$', $mediaId, $provider->getUrlScheme());
        $queryStringSuffix = (!empty($parameters)) ? '?' . http_build_query($parameters) : '';

        return sprintf('%s?url=%s%s', $provider->getApiEndpoint(), $mediaUrl, rawurlencode($queryStringSuffix));
    }
}
