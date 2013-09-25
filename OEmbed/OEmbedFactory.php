<?php

namespace Markup\OEmbedBundle\OEmbed;

use Markup\OEmbedBundle\Exception\InvalidOEmbedContentException;
use Markup\OEmbedBundle\Provider\ProviderInterface;

/**
* A factory for creating oEmbed objects from inputs.
*/
class OEmbedFactory
{
    /**
     * Creates an oEmbed object from some JSON output by an oEmbed service.
     *
     * @param  string            $json
     * @param  ProviderInterface $provider
     * @return OEmbedInterface
     **/
    public function createFromJson($json, ProviderInterface $provider)
    {
        $properties = json_decode($json, $assoc = true);
        if (null === $properties || !isset($properties['version']) || !isset($properties['type'])) {
            throw new InvalidOEmbedContentException(sprintf('The returned oEmbed content from provider "%s" was invalid.', $provider->getName()));
        }

        return new OEmbed($properties['type'], $properties, $provider->getEmbedCodeProperty());
    }
}
