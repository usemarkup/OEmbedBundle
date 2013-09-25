<?php

namespace Markup\OEmbedBundle\Cache;

/**
* A null cache implementation.
*/
class NullCache implements CacheInterface
{
    /**
     * {@inheritdoc}
     **/
    public function get($key)
    {
    }

    /**
     * {@inheritdoc}
     **/
    public function set($key, $value)
    {
    }
}
