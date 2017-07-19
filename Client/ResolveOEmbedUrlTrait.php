<?php

namespace Markup\OEmbedBundle\Client;

use Markup\OEmbedBundle\Provider\ProviderInterface;

trait ResolveOEmbedUrlTrait
{
    /**
     * Resolves the media ID and an oEmbed provider to a URL.
     *
     * @param  ProviderInterface $provider
     * @param  string            $mediaId
     * @param  array             $parameters
     * @return string
     **/
    protected function resolveOEmbedUrl(ProviderInterface $provider, $mediaId, array $parameters = [])
    {
        $mediaUrl = str_replace('$ID$', $mediaId, $provider->getUrlScheme());

        $mediaUrlHasQueryString = (bool) parse_url($mediaUrl, PHP_URL_QUERY);
        $queryStingPrefix = ($mediaUrlHasQueryString) ? '&' : '?';

        $queryStringSuffix = (!empty($parameters)) ? $queryStingPrefix . http_build_query($parameters) : '';
        return sprintf(
            '%s?url=%s%s',
            $provider->getApiEndpoint(),
            $mediaUrl,
            rawurlencode($queryStringSuffix)
        );
    }
}
