<?php

namespace Markup\OEmbedBundle\Client;

use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Markup\OEmbedBundle\OEmbed\OEmbedFactory;
use Markup\OEmbedBundle\Provider\ProviderInterface;
use Buzz\Browser as Buzz;
use Buzz\Exception\ExceptionInterface as BuzzException;

/**
* An oEmbed client that uses Buzz.
*/
class BuzzClient extends AbstractClient
{
    /**
     * @var Buzz
     **/
    private $buzz;

    /**
     * @var OEmbedFactory
     **/
    private $oEmbedFactory;

    /**
     * @param Buzz          $buzz
     * @param OEmbedFactory $oEmbedFactory
     **/
    public function __construct(Buzz $buzz, OEmbedFactory $oEmbedFactory)
    {
        $this->buzz = $buzz;
        $this->oEmbedFactory = $oEmbedFactory;
    }

    /**
     * {@inheritdoc}
     **/
    public function fetchEmbed(ProviderInterface $provider, $mediaId, array $parameters = array())
    {
        $oEmbedUrl = $this->resolveOEmbedUrl($provider, $mediaId, $parameters);
        try {
            $response = $this->buzz->get($oEmbedUrl);
        } catch (BuzzException $buzzException) {
            //TODO: log this
            $response = false;
        }
        if (!$response || !$response->isOk()) {
            throw new OEmbedUnavailableException(sprintf('Trying to get the embed code for media ID %s from provider "%s" failed.', $mediaId, $provider->getName()), 0, (isset($buzzException)) ? $buzzException : null);
        }

        return $this->oEmbedFactory->createFromJson($response->getContent(), $provider);
    }
}
