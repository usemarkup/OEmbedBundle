<?php

namespace Markup\OEmbedBundle\Serializer;

use Markup\OEmbedBundle\OEmbed;

/**
* A serializer for oEmbed instances.
*/
class OEmbedSerializer implements OEmbedSerializerInterface
{
    /**
     * {@inheritdoc}
     **/
    public function serialize(OEmbed\OEmbedInterface $oEmbed)
    {
        return json_encode($oEmbed);
    }

    /**
     * {@inheritdoc}
     **/
    public function deserialize($serialized)
    {
        $oEmbedArray = json_decode($serialized, $assoc = true);

        return new OEmbed\OEmbed($oEmbedArray['type'], $oEmbedArray['properties'], $oEmbedArray['embed_property']);
    }
}
