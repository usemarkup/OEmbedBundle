<?php

namespace Markup\OEmbedBundle\Tests\Provider;

use Markup\OEmbedBundle\Provider\SimpleProvider;

/**
* A test for a simple provider implementation.
*/
class SimpleProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->name = 'provider';
        $this->endpoint = 'http://endpoint.com/oembed';
        $this->scheme = 'http://my.thing.com/%ID%';
        $this->embedProperty = 'code';
        $this->provider = new SimpleProvider($this->name, $this->endpoint, $this->scheme, $this->embedProperty);
    }

    public function testIsProvider()
    {
        $this->assertInstanceOf('Markup\OEmbedBundle\Provider\ProviderInterface', $this->provider);
    }

    public function testGetName()
    {
        $this->assertEquals($this->name, $this->provider->getName());
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
