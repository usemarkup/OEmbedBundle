<?php

namespace Markup\OEmbedBundle\Cache;

use Markup\OEmbedBundle\OEmbed\OEmbedInterface;

/**
 * An interface for caching an oEmbed object.
 **/
interface ObjectCacheInterface
{
    /**
     * Gets the oEmbed instance at the given key. Returns null on cache miss.
     *
     * @param  string               $key
     * @return OEmbedInterface|null
     **/
    public function get($key);

    /**
     * Sets an oEmbed instance into the cache.
     *
     * @param string          $key
     * @param OEmbedInterface $oEmbed
     **/
    public function set($key, OEmbedInterface $oEmbed);
}
