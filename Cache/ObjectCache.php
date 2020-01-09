<?php

namespace Markup\OEmbedBundle\Cache;

use Markup\OEmbedBundle\OEmbed\OEmbedInterface;
use Markup\OEmbedBundle\Serializer\OEmbedSerializerInterface;

/**
* An object cache for oEmbed instances.
*/
class ObjectCache implements ObjectCacheInterface
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     **/
    public function set($key, OEmbedInterface $oEmbed)
    {
        $this->cache->set($key, $this->serializer->serialize($oEmbed));
    }
}
