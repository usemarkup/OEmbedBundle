<?php

namespace Markup\OEmbedBundle\OEmbed;

/**
* A reference to an OEmbed instance
*/
class Reference
{
    /**
     * @var string
     **/
    private $mediaId;

    /**
     * @var string
     **/
    private $provider;

    /**
     * @param string $mediaId  The individual media ID for an OEmbed instance.
     * @param string $provider The name of the OEmbed provider being used here.
     **/
    public function __construct($mediaId, $provider)
    {
        $this->mediaId = $mediaId;
        $this->provider = $provider;
    }

    /**
     * Gets the individual media ID for an OEmbed instance.
     *
     * @return string
     **/
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * Gets the name of the OEmbed provider being used here.
     *
     * @return string
     **/
    public function getProvider()
    {
        return $this->provider;
    }
}
