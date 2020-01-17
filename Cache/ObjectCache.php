<?php

namespace Markup\OEmbedBundle\Cache;

use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Serializer\OEmbedSerializerInterface;

/**
* An object cache for oEmbed instances.
*/
class ObjectCache
{
    /**
     * @var CacheInterface
     **/
    private $cache;

    /**
     * @var OEmbedSerializerInterface
     **/
    private $serializer;

    public function __construct(CacheInterface $cache, OEmbedSerializerInterface $serializer)
    {
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @param string $key
     * @return OEmbedInterface|null
     **/
    public function get($key)
    {
        $cached = $this->cache->get($key);
        if (null === $cached) {
            return null;
        }

        return $this->serializer->deserialize($cached);
    }

    /**
     * @param string $key
     * @param OEmbedInterface $oEmbed
     * @return void
     **/
    public function set($key, OEmbedInterface $oEmbed)
    {
        $this->cache->set($key, $this->serializer->serialize($oEmbed));
    }
}
