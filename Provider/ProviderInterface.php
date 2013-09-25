<?php

namespace Markup\OEmbedBundle\Provider;

/**
 * An interface for an oEmbed provider.
 **/
interface ProviderInterface
{
    /**
     * Gets the API endpoint to use.
     *
     * @return string
     **/
    public function getApiEndpoint();

    /**
     * Gets the URL scheme or individual pieces of media. The placeholder for the media ID that MUST be used is '$ID$'.
     *
     * @return string
     **/
    public function getUrlScheme();

    /**
     * Gets the property to use for retrieving the raw embed code from an oEmbed object.
     *
     * @return string
     **/
    public function getEmbedCodeProperty();

    /**
     * Gets the name of the provider.
     *
     * @return string
     **/
    public function getName();
}
