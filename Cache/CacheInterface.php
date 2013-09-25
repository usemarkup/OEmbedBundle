<?php

namespace Markup\OEmbedBundle\Cache;

/**
 * An interface for a cache of oEmbed lookups.
 * NB. The intention is to replace this interface with any appropriate PSR for caching if/when it is agreed (@see https://github.com/php-fig/fig-standards/pull/17/files)
 **/
interface CacheInterface
{
    /**
     * Gets the content of the given key. Returns null for a cache miss.
     *
     * @param  string      $key
     * @return string|null
     **/
    public function get($key);

    /**
     * Sets some content against a key.
     *
     * @param string $key
     * @param string $value
     **/
    public function set($key, $value);
}
