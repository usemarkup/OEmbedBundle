<?php

namespace Markup\OEmbedBundle\Tests\Provider;

use Markup\OEmbedBundle\Provider\ProviderInterface;
use Markup\OEmbedBundle\Provider\SimpleProvider;
use PHPUnit\Framework\TestCase;

/**
* A test for a simple provider implementation.
*/
class SimpleProviderTest extends TestCase
{
    public function setUp()
    {
        $this->providerName = 'provider';
        $this->endpoint = 'http://endpoint.com/oembed';
        $this->scheme = 'http://my.thing.com/%ID%';
        $this->embedProperty = 'code';
        $this->provider = new SimpleProvider($this->providerName, $this->endpoint, $this->scheme, $this->embedProperty);
    }

    public function testIsProvider()
    {
        $this->assertInstanceOf(ProviderInterface::class, $this->provider);
    }

    public function testGetName()
    {
        $this->assertEquals($this->providerName, $this->provider->getName());
    }

    public function testGetApiEndpoint()
    {
        $this->assertEquals($this->endpoint, $this->provider->getApiEndpoint());
    }

    public function testGetUrlScheme()
    {
        $this->assertEquals($this->scheme, $this->provider->getUrlScheme());
    }

    public function testGetEmbedCodeProperty()
    {
        $this->assertEquals($this->embedProperty, $this->provider->getEmbedCodeProperty());
    }
}
