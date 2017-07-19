<?php

namespace Markup\OEmbedBundle\Serializer;

use Markup\OEmbedBundle\OEmbed\OEmbedInterface;

/**
 * An interface for an object that can serlialize an OEmbed object into a class-agnostic form and also unserialize it.
 **/
interface OEmbedSerializerInterface
{
    /**
     * Serializes an oEmbed instance into a string.
     *
     * @param  OEmbedInterface $oEmbed
     * @return mixed
     **/
    public function serialize(OEmbedInterface $oEmbed);

    /**
     * Deserializes an oEmbed serialization into an instance.
     *
     * @param  string          $serialized
     * @return OEmbedInterface
     **/
    public function deserialize($serialized): OEmbedInterface;
}
