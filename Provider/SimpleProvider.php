<?php

namespace Markup\OEmbedBundle\Provider;

/**
* A simple provider implementation.
*/
class SimpleProvider implements ProviderInterface
{
    /**
     * @var string
     **/
    private $name;

    /**
     * The API endpoint.
     *
     * @var string
     **/
    private $endpoint;

    /**
     * The URL scheme.
     *
     * @var string
     **/
    private $scheme;

    /**
     * @var string
     **/
    private $embedProperty;

    /**
     * @param string $name
     * @param string $endpoint
     * @param string $scheme
     * @param string $embedProperty
     **/
    public function __construct($name, $endpoint, $scheme, $embedProperty = 'code')
    {
        $this->name = $name;
        $this->endpoint = $endpoint;
        $this->scheme = $scheme;
        $this->embedProperty = $embedProperty;
    }

    /**
     * {@inheritdoc}
     **/
    public function getApiEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * {@inheritdoc}
     **/
    public function getUrlScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     **/
    public function getEmbedCodeProperty()
    {
        return $this->embedProperty;
    }

    /**
     * {@inheritdoc}
     **/
    public function getName()
    {
        return $this->name;
    }
}
