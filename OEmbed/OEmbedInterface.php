<?php

namespace Markup\OEmbedBundle\OEmbed;

use JsonSerializable;

/**
 * An interface for an oEmbed object.
 **/
interface OEmbedInterface extends JsonSerializable
{
    /**
     * Gets the oEmbed type - @see http://oembed.com/#section2
     *
     * @return string
     **/
    public function getType();

    /**
     * Gets whether the object has the provided property.
     *
     * @param  string $property
     * @return bool
     **/
    public function has($property);

    /**
     * Gets the value for the provided property.
     *
     * @param string $property
     * @return mixed|null
     **/
    public function get($property);

    /**
     * Fetches all defined properties.
     *
     * @return array
     **/
    public function all();

    /**
     * Gets the embed code to use for embedding this media document.  Returns null if this cannot be automatically determined.
     *
     * @return string|null
     **/
    public function getEmbedCode();
}
